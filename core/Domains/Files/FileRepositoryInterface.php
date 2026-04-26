<?php declare(strict_types=1);

namespace Core\Domains\Files;

use Core\Repositories\SearcherInterface;

interface FileRepositoryInterface
{
    public function search(SearcherInterface $searcher): FileSearchResponse;

    public function save(FileEntity $dto): FileEntity;

    public function getById(?int $id): ?FileEntity;

    public function getByIds(array $ids): FileSearchResponse;

    public function deleteById(?int $id): bool;

    /**
     * @return int[]
     */
    public function getIdsByFullTextSearch(string $search): array;
}
