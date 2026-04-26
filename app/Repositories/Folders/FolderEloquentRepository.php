<?php declare(strict_types=1);

namespace App\Repositories\Folders;

use App\Models\File\FolderModel;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Shared\Collections\Collection;
use Core\Domains\Folders\FolderCollection;
use Core\Domains\Folders\FolderEntity;
use Core\Domains\Folders\FolderRepositoryInterface;
use Core\Domains\Folders\FolderSearcher;
use Core\Domains\Folders\FolderSearchResponse;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use ReturnTypeWillChange;

class FolderEloquentRepository implements FolderRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly FolderEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return FolderModel::class;
    }

    protected function getTable(): string
    {
        return FolderModel::TABLE;
    }

    protected function getEmptyCollection(): Collection
    {
        return new FolderCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return FolderSearchResponse
     */
    protected function getEmptySearchResponse(): FolderSearchResponse
    {
        return new FolderSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return FolderSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new FolderSearcher();
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): FolderSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(FolderEntity $folder): FolderEntity
    {
        /** @var FolderModel|null $model */
        $model = $this->getModelById($folder->getId());
        $model = $this->mapper->makeRepositoryDataFromEntity($folder, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?FolderEntity
    {
        /** @var FolderModel|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }
}
