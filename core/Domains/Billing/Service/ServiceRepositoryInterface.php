<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service;

use Core\Repositories\SearcherInterface;

interface ServiceRepositoryInterface
{
    public function search(SearcherInterface $searcher): ServiceSearchResponse;

    public function save(ServiceEntity $service): ServiceEntity;

    public function getById(?int $id): ?ServiceEntity;

    public function getByIds(array $ids): ServiceSearchResponse;

    public function deleteById(?int $id): bool;
}
