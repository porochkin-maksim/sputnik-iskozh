<?php declare(strict_types=1);

namespace App\Repositories\CounterHistory;

use App\Models\Counter\CounterHistory;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Repositories\RepositoryConfig;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryRepositoryInterface;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\CounterHistory\CounterHistorySearchResponse;
use Core\Repositories\SearcherInterface;

class CounterHistoryEloquentRepository implements CounterHistoryRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly CounterHistoryEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : CounterHistory::class,
            table              : CounterHistory::TABLE,
            collectionClass    : CounterHistoryCollection::class,
            searchResponseClass: CounterHistorySearchResponse::class,
            searcherClass      : CounterHistorySearcher::class,
        );
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
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
