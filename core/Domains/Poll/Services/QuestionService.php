<?php declare(strict_types=1);

namespace Core\Domains\Poll\Services;

use Core\Domains\Poll\Collections\QuestionCollection;
use Core\Domains\Poll\Factories\QuestionFactory;
use Core\Domains\Poll\Models\QuestionDTO;
use Core\Domains\Poll\Models\Responses\QuestionSearchResponse;
use Core\Domains\Poll\Models\Searchers\QuestionSearcher;
use Core\Domains\Poll\Repositories\QuestionRepository;

readonly class QuestionService
{
    public function __construct(
        private QuestionFactory    $questionFactory,
        private QuestionRepository $questionRepository,
    )
    {
    }

    public function save(QuestionDTO $question): QuestionDTO
    {
        $model = $this->questionRepository->getById($question->getId());

            $model = $this->questionRepository->save($this->questionFactory->makeModelFromDto($question, $model));

            return $this->questionFactory->makeDtoFromObject($model);
    }

    public function search(QuestionSearcher $searcher): QuestionSearchResponse
    {
        $response = $this->questionRepository->search($searcher);

        $result = new QuestionSearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new QuestionCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->questionFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?QuestionDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new QuestionSearcher();
        $searcher->setId($id)
            ->setWithPoll();
        $result = $this->questionRepository->search($searcher)->getItems()->first();

        return $result ? $this->questionFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        return $this->questionRepository->deleteById($id);
    }
}
