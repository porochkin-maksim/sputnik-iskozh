<?php declare(strict_types=1);

namespace Core\Domains\HistoryChanges;

use Core\Repositories\SearcherInterface;

interface HistoryChangesRepositoryInterface
{
    public function search(SearcherInterface $searcher): HistoryChangesSearchResponse;
    public function getById(?int $id): ?HistoryChangesEntity;
    public function save(HistoryChangesEntity $entity): HistoryChangesEntity;
    public function deleteById(?int $id): bool;
}
