<?php declare(strict_types=1);

namespace Core\Objects\News\Services;

use Core\Objects\News\Collections\NewsCollection;
use Core\Objects\News\Factories\NewsFactory;
use Core\Objects\News\Models\NewsDTO;
use Core\Objects\News\Models\NewsSearcher;
use Core\Objects\News\Repositories\NewsRepository;
use Core\Objects\News\Responses\SearchResponse;

readonly class NewsService
{
    public function __construct(
        private NewsFactory    $newsFactory,
        private NewsRepository $newsRepository,
    )
    {
    }

    public function save(NewsDTO $dto): NewsDTO
    {
        $report = null;

        if ($dto->getId()) {
            $report = $this->newsRepository->getById($dto->getId());
        }

        if ( ! $dto->getPublishedAt()) {
            $dto->setPublishedAt(now());
        }

        $report = $this->newsFactory->makeModelFromDto($dto, $report);
        $report = $this->newsRepository->save($report);

        return $this->newsFactory->makeDtoFromObject($report);
    }

    public function search(NewsSearcher $searcher): SearchResponse
    {
        $response = $this->newsRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new NewsCollection();
        foreach ($response->getItems() as $report) {
            $collection->add($this->newsFactory->makeDtoFromObject($report));
        }

        return $result->setItems($collection);
    }

    public function getById(int $id): ?NewsDTO
    {
        $result = $this->newsRepository->getById($id);

        return $result ? $this->newsFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        return $this->newsRepository->deleteById($id);
    }
}
