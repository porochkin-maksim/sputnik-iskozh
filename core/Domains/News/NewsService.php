<?php declare(strict_types=1);

namespace Core\Domains\News;

readonly class NewsService
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository,
    )
    {
    }

    public function save(NewsEntity $news): NewsEntity
    {
        if ( ! $news->getPublishedAt()) {
            $news->setPublishedAt(now());
        }

        if ( ! trim(strip_tags((string) $news->getArticle()))) {
            $news->setArticle('');
        }

        return $this->newsRepository->save($news);
    }

    public function search(NewsSearcher $searcher): NewsSearchResponse
    {
        return $this->newsRepository->search($searcher);
    }

    /**
     * @return int[]
     */
    public function getIdsByFullTextSearch(NewsSearcher $searcher): array
    {
        if ($searcher->getSearch()) {
            return $this->newsRepository->getIdsByFullTextSearch($searcher->getSearch());
        }

        return [];
    }

    public function getById(?int $id): ?NewsEntity
    {
        return $this->newsRepository->getById($id);
    }

    public function deleteById(int $id): bool
    {
        return $this->newsRepository->deleteById($id);
    }
}
