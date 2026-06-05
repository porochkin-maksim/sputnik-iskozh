<?php declare(strict_types=1);

namespace App\Repositories\Option;

use App\Models\Infra\Option;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Option\OptionCollection;
use Core\Domains\Option\OptionEntity;
use Core\Domains\Option\OptionRepositoryInterface;
use Core\Domains\Option\OptionSearcher;
use Core\Domains\Option\OptionSearchResponse;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;

class OptionEloquentRepository implements OptionRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly OptionEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : Option::class,
            table              : Option::TABLE,
            collectionClass    : OptionCollection::class,
            searchResponseClass: OptionSearchResponse::class,
            searcherClass      : OptionSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): OptionSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(OptionEntity $entity): OptionEntity
    {
        $model = $this->getModelById($entity->getId());
        $model = $this->mapper->makeRepositoryDataFromEntity($entity, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?OptionEntity
    {
        /** @var Option|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }
}
