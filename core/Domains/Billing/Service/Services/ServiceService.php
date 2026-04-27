<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Services;

use Core\Domains\Billing\Service\Collections\ServiceCollection;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceDTO;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\Repositories\ServiceRepository;
use Core\Domains\Billing\Service\Responses\ServiceSearchResponse;

readonly class ServiceService
{
    public function __construct(
        private ServiceRepository $serviceRepository,
    )
    {
    }

    public function search(ServiceSearcher $searcher): ServiceSearchResponse
    {
        return $this->serviceRepository->search($searcher);
    }

    public function getById(?int $id): ?ServiceDTO
    {
        return $this->serviceRepository->getById($id);
    }

    public function save(ServiceDTO $service): ServiceDTO
    {
        return $this->serviceRepository->save($service);
    }

    public function deleteById(?int $id): bool
    {
        return $this->serviceRepository->deleteById($id);
    }

    public function getByPeriodIdAndType(int $periodId, ServiceTypeEnum $type): ?ServiceDTO
    {
        $searcher = new ServiceSearcher()
            ->setPeriodId($periodId)
            ->setType($type)
        ;

        return $this->search($searcher)->getItems()->first();
    }

    public function getByPeriodId(?int $periodId): ServiceCollection
    {
        $searcher = new ServiceSearcher()->setPeriodId($periodId);

        return $this->search($searcher)->getItems();
    }
}
