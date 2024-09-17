<?php declare(strict_types=1);

namespace Core\Domains\Poll\Services;

use Core\Domains\Poll\Collections\AnswerCollection;
use Core\Domains\Poll\Factories\AnswerFactory;
use Core\Domains\Poll\Models\AnswerDTO;
use Core\Domains\Poll\Models\Responses\AnswerSearchResponse;
use Core\Domains\Poll\Models\Searchers\AnswerSearcher;
use Core\Domains\Poll\Repositories\AnswerRepository;

readonly class AnswerService
{
    public function __construct(
        private AnswerFactory    $answerFactory,
        private AnswerRepository $answerRepository,
    )
    {
    }

    public function save(AnswerDTO $answer): AnswerDTO
    {
        $model = $this->answerRepository->getById($answer->getId());

            $model = $this->answerRepository->save($this->answerFactory->makeModelFromDto($answer, $model));

            return $this->answerFactory->makeDtoFromObject($model);
    }

    public function search(AnswerSearcher $searcher): AnswerSearchResponse
    {
        $response = $this->answerRepository->search($searcher);

        $result = new AnswerSearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new AnswerCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->answerFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?AnswerDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new AnswerSearcher();
        $searcher->setId($id)
            ->setWithQuestion();
        $result = $this->answerRepository->search($searcher)->getItems()->first();

        return $result ? $this->answerFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        return $this->answerRepository->deleteById($id);
    }
}
