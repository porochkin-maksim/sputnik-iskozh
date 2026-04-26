<?php declare(strict_types=1);

namespace Core\Domains\Counter;

use Core\Repositories\SearcherInterface;

interface CounterRepositoryInterface
{
    public function search(SearcherInterface $searcher): CounterSearchResponse;
    public function getById(?int $id): ?CounterEntity;
    public function save(CounterEntity $entity): CounterEntity;
    public function deleteById(?int $id): bool;
}
