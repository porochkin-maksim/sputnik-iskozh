<?php declare(strict_types=1);

namespace App\Repositories\HistoryChanges;

use App\Models\Infra\HistoryChanges;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
use Core\Domains\HistoryChanges\HistoryChangesCollection;
use Core\Domains\HistoryChanges\HistoryChangesRepositoryInterface;
use Core\Domains\HistoryChanges\HistoryChangesSearcher;
use Core\Domains\HistoryChanges\HistoryChangesSearchResponse;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;

class HistoryChangesEloquentRepository implements HistoryChangesRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly HistoryChangesEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : HistoryChanges::class,
            table              : HistoryChanges::TABLE,
            collectionClass    : HistoryChangesCollection::class,
            searchResponseClass: HistoryChangesSearchResponse::class,
            searcherClass      : HistoryChangesSearcher::class,
        );
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): HistoryChangesSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id): ?HistoryChangesEntity
    {
        /** @var ?HistoryChanges $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function save(HistoryChangesEntity $entity): HistoryChangesEntity
    {
        /** @var ?HistoryChanges $model */
        $model = $this->getModelById($entity->getId());
        /** @var HistoryChanges $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($entity, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }
}
