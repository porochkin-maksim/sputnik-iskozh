<?php declare(strict_types=1);

namespace App\Repositories\Counter;

use App\Models\Counter\Counter;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Counter\CounterCollection;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterRepositoryInterface;
use Core\Domains\Counter\CounterSearchResponse;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class CounterEloquentRepository implements CounterRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly CounterEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return Counter::class;
    }

    protected function getTable(): string
    {
        return Counter::TABLE;
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new CounterCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return CounterSearchResponse
     */
    protected function getEmptySearchResponse(): CounterSearchResponse
    {
        return new CounterSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return CounterSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new CounterSearcher();
    }

    public function search(SearcherInterface $searcher): CounterSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id): ?CounterEntity
    {
        /** @var ?Counter $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function save(CounterEntity $entity): CounterEntity
    {
        /** @var ?Counter $model */
        $model = $this->getModelById($entity->getId());
        /** @var Counter $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($entity, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }
}
