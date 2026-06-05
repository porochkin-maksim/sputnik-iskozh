<?php declare(strict_types=1);

namespace Core\Domains\News;

use Core\Repositories\SearcherInterface;

interface NewsRepositoryInterface
{
    public function search(SearcherInterface $searcher): NewsSearchResponse;

    public function save(NewsEntity $news): NewsEntity;

    public function getById(?int $id): ?NewsEntity;

    public function getByIds(array $ids): NewsSearchResponse;

    public function deleteById(?int $id): bool;

    /**
     * @return int[]
     */
    public function getIdsByFullTextSearch(string $search): array;
}
