<?php declare(strict_types=1);

namespace Core\Domains\News\Services;

use Core\Domains\News\Collections\NewsCollection;
use Core\Domains\News\Factories\NewsFactory;
use Core\Domains\News\Models\NewsDTO;
use Core\Domains\News\Models\NewsSearcher;
use Core\Domains\News\Repositories\NewsRepository;
use Core\Domains\News\Responses\SearchResponse;

readonly class NewsService
{
    public function __construct(
        private NewsFactory    $newsFactory,
        private NewsRepository $newsRepository,
    )
    {
    }

    public function save(NewsDTO $news): NewsDTO
    {
        $model = $this->newsRepository->getById($news->getId());

        if ( ! $news->getPublishedAt()) {
            $news->setPublishedAt(now());
        }

        if ( ! trim(strip_tags((string) $news->getArticle()))) {
            $news->setArticle('');
        }

        $model = $this->newsRepository->save($this->newsFactory->makeModelFromDto($news, $model));

        return $this->newsFactory->makeDtoFromObject($model);
    }

    public function search(NewsSearcher $searcher): SearchResponse
    {
        $response = $this->newsRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new NewsCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->newsFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
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

    public function getById(?int $id): ?NewsDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new NewsSearcher();
        $searcher->setId($id)
            ->setWithFiles();
        $result = $this->newsRepository->search($searcher)->getItems()->first();

        return $result ? $this->newsFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        return $this->newsRepository->deleteById($id);
    }
}
