<?php declare(strict_types=1);

namespace Core\Domains\Folders;

use Core\Repositories\SearcherInterface;

interface FolderRepositoryInterface
{
    public function search(SearcherInterface $searcher): FolderSearchResponse;

    public function save(FolderEntity $folder): FolderEntity;

    public function getById(?int $id): ?FolderEntity;

    public function deleteById(?int $id): bool;
}
