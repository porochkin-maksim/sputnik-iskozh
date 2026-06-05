<?php declare(strict_types=1);

namespace App\Repositories\Counter;

use App\Models\Counter\Counter;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Repositories\RepositoryConfig;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Counter\CounterCollection;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterRepositoryInterface;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterSearchResponse;
use Core\Repositories\SearcherInterface;

class CounterEloquentRepository implements CounterRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly CounterEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : Counter::class,
            table              : Counter::TABLE,
            collectionClass    : CounterCollection::class,
            searchResponseClass: CounterSearchResponse::class,
            searcherClass      : CounterSearcher::class,
        );
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
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
