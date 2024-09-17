<?php declare(strict_types=1);

namespace Core\Domains\Poll\Services;

use Core\Domains\Poll\Collections\PollCollection;
use Core\Domains\Poll\Factories\PollFactory;
use Core\Domains\Poll\Models\PollDTO;
use Core\Domains\Poll\Models\Responses\PollSearchResponse;
use Core\Domains\Poll\Models\Searchers\PollSearcher;
use Core\Domains\Poll\Repositories\PollRepository;

readonly class PollService
{
    public function __construct(
        private PollFactory    $pollFactory,
        private PollRepository $pollRepository,
    )
    {
    }

    public function save(PollDTO $poll): PollDTO
    {
        $model = $this->pollRepository->getById($poll->getId());
        $model = $this->pollRepository->save($this->pollFactory->makeModelFromDto($poll, $model));

        return $this->pollFactory->makeDtoFromObject($model);
    }

    public function search(PollSearcher $searcher): PollSearchResponse
    {
        $response = $this->pollRepository->search($searcher);

        $result = new PollSearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new PollCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->pollFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?PollDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new PollSearcher();
        $searcher->setId($id)
            ->setWithQuestions();
        $result = $this->pollRepository->search($searcher)->getItems()->first();

        return $result ? $this->pollFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        return $this->pollRepository->deleteById($id);
    }
}
