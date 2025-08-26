<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Services;

use Core\Domains\Billing\Service\Collections\ServiceCollection;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Factories\ServiceFactory;
use Core\Domains\Billing\Service\Models\ServiceComparator;
use Core\Domains\Billing\Service\Models\ServiceDTO;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\Repositories\ServiceRepository;
use Core\Domains\Billing\Service\Responses\SearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

readonly class ServiceService
{
    public function __construct(
        private ServiceFactory        $serviceFactory,
        private ServiceRepository     $serviceRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(ServiceDTO $service): ServiceDTO
    {
        $model = $this->serviceRepository->getById($service->getId());
        if ($model) {
            $before = $this->serviceFactory->makeDtoFromObject($model);
        }
        else {
            $before = new ServiceDTO();
        }

        if ($service->getId() && $service->getType() !== $before->getType()) {
            $service->setType($before->getType());
        }

        $model   = $this->serviceRepository->save($this->serviceFactory->makeModelFromDto($service, $model));
        $current = $this->serviceFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $service->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::PERIOD,
            $current->getPeriodId(),
            HistoryType::SERVICE,
            $current->getId(),
            new ServiceComparator($current),
            new ServiceComparator($before),
        );

        return $current;
    }

    public function search(ServiceSearcher $searcher): SearchResponse
    {
        $response = $this->serviceRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new ServiceCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->serviceFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?ServiceDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new ServiceSearcher();
        $searcher->setId($id);
        $result = $this->serviceRepository->search($searcher)->getItems()->first();

        return $result ? $this->serviceFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        $service = $this->getById($id);

        if ( ! $service) {
            return false;
        }

        if ($service->getType() !== ServiceTypeEnum::TARGET_FEE) {
            return false;
        }

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::PERIOD,
            $service->getPeriodId(),
            HistoryType::SERVICE,
            $service->getId(),
        );

        return $this->serviceRepository->deleteById($id);
    }

    public function getByPeriodIdAndType(int $periodId, ServiceTypeEnum $case): ?ServiceDTO
    {
        return $this->search(
            ServiceSearcher::make()
                ->setPeriodId($periodId)
                ->setType($case),
        )->getItems()->first();
    }
}
