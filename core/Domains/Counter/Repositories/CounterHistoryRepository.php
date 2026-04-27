<?php declare(strict_types=1);

namespace Core\Domains\Counter\Repositories;

use App\Models\Counter\CounterHistory;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Counter\Collections\CounterHistoryCollection;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Counter\Responses\CounterHistorySearchResponse;

class CounterHistoryRepository
{
    use RepositoryTrait;

    private const string TABLE = CounterHistory::TABLE;

    public function __construct(
        private readonly CounterHistoryFactory $factory,
    )
    {
    }

    protected function modelClass(): string
    {
        return CounterHistory::class;
    }

    public function search(SearcherInterface $searcher): CounterHistorySearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new CounterHistoryCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->factory->makeDtoFromObject($model));
        }

        $result = new CounterHistorySearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function getById(?int $id): ?CounterHistoryDTO
    {
        /** @var null|CounterHistory $model */
        $model = $this->getModelById($id);

        return $model ? $this->factory->makeDtoFromObject($model) : null;
    }

    public function save(CounterHistoryDTO $dto): CounterHistoryDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->factory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->factory->makeDtoFromObject($model);
    }
}
