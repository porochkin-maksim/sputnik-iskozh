<?php declare(strict_types=1);

namespace App\Repositories\CounterHistory;

use App\Models\Counter\CounterHistory;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryRepositoryInterface;
use Core\Domains\CounterHistory\CounterHistorySearchResponse;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class CounterHistoryEloquentRepository implements CounterHistoryRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly CounterHistoryEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return CounterHistory::class;
    }

    protected function getTable(): string
    {
        return CounterHistory::TABLE;
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new CounterHistoryCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return CounterHistorySearchResponse
     */
    protected function getEmptySearchResponse(): CounterHistorySearchResponse
    {
        return new CounterHistorySearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return CounterHistorySearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new CounterHistorySearcher();
    }

    public function search(SearcherInterface $searcher): CounterHistorySearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id): ?CounterHistoryEntity
    {
        /** @var ?CounterHistory $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function save(CounterHistoryEntity $entity): CounterHistoryEntity
    {
        /** @var ?CounterHistory $model */
        $model = $this->getModelById($entity->getId());
        /** @var CounterHistory $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($entity, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }
}
