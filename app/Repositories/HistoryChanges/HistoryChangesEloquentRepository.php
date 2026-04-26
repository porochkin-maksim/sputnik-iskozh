<?php declare(strict_types=1);

namespace App\Repositories\HistoryChanges;

use App\Models\Infra\HistoryChanges;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\HistoryChanges\HistoryChangesCollection;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
use Core\Domains\HistoryChanges\HistoryChangesRepositoryInterface;
use Core\Domains\HistoryChanges\HistoryChangesSearchResponse;
use Core\Domains\HistoryChanges\HistoryChangesSearcher;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class HistoryChangesEloquentRepository implements HistoryChangesRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly HistoryChangesEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return HistoryChanges::class;
    }

    protected function getTable(): string
    {
        return HistoryChanges::TABLE;
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new HistoryChangesCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return HistoryChangesSearchResponse
     */
    protected function getEmptySearchResponse(): HistoryChangesSearchResponse
    {
        return new HistoryChangesSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return HistoryChangesSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new HistoryChangesSearcher();
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
