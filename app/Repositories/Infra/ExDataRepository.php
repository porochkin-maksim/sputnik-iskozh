<?php declare(strict_types=1);

namespace App\Repositories\Infra;

use App\Models\Infra\ExData;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Infra\ExData\ExDataCollection;
use Core\Domains\Infra\ExData\ExDataEntity;
use Core\Domains\Infra\ExData\Models\ExDataSearcher;
use Core\Domains\Infra\ExData\Repositories\ExDataRepositoryInterface;
use Core\Repositories\BaseSearchResponse;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;

class ExDataRepository implements ExDataRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly ExDataEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : ExData::class,
            table              : ExData::TABLE,
            collectionClass    : ExDataCollection::class,
            searchResponseClass: BaseSearchResponse::class,
            searcherClass      : ExDataSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): BaseSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(ExDataEntity $entity): ExDataEntity
    {
        /** @var ?ExData $model */
        $model = $this->getModelById($entity->getId());
        /** @var ExData $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($entity, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?ExDataEntity
    {
        /** @var ?ExData $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function deleteById(?int $id): bool
    {
        return $this->deleteModelById($id);
    }
}
