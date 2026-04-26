<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory;

use Core\Repositories\SearcherInterface;

interface CounterHistoryRepositoryInterface
{
    public function search(SearcherInterface $searcher): CounterHistorySearchResponse;
    public function getById(?int $id): ?CounterHistoryEntity;
    public function save(CounterHistoryEntity $entity): CounterHistoryEntity;
    public function deleteById(?int $id): bool;
}
