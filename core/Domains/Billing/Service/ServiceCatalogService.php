<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service;

readonly class ServiceCatalogService
{
    public function __construct(
        private ServiceRepositoryInterface $serviceRepository,
    )
    {
    }

    public function search(ServiceSearcher $searcher): ServiceSearchResponse
    {
        return $this->serviceRepository->search($searcher);
    }

    public function getById(?int $id): ?ServiceEntity
    {
        return $this->serviceRepository->getById($id);
    }

    public function save(ServiceEntity $service): ServiceEntity
    {
        return $this->serviceRepository->save($service);
    }

    public function deleteById(?int $id): bool
    {
        return $this->serviceRepository->deleteById($id);
    }

    public function getByPeriodIdAndType(int $periodId, ServiceTypeEnum $type): ?ServiceEntity
    {
        $searcher = new ServiceSearcher();
        $searcher
            ->setPeriodId($periodId)
            ->setType($type)
        ;

        return $this->search($searcher)->getItems()->first();
    }

    public function getByPeriodId(?int $periodId): ServiceCollection
    {
        $searcher = new ServiceSearcher();
        $searcher->setPeriodId($periodId);

        return $this->search($searcher)->getItems();
    }
}
