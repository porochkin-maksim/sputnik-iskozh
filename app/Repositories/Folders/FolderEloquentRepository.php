<?php declare(strict_types=1);

namespace App\Repositories\Folders;

use App\Models\File\FolderModel;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Repositories\RepositoryConfig;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Folders\FolderCollection;
use Core\Domains\Folders\FolderEntity;
use Core\Domains\Folders\FolderRepositoryInterface;
use Core\Domains\Folders\FolderSearcher;
use Core\Domains\Folders\FolderSearchResponse;
use Core\Repositories\SearcherInterface;

class FolderEloquentRepository implements FolderRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly FolderEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : FolderModel::class,
            table              : FolderModel::TABLE,
            collectionClass    : FolderCollection::class,
            searchResponseClass: FolderSearchResponse::class,
            searcherClass      : FolderSearcher::class,
        );
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
