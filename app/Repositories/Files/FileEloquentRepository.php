<?php declare(strict_types=1);

namespace App\Repositories\Files;

use App\Models\Files\FileModel;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Shared\Collections\Collection;
use Core\Domains\Files\FileCollection;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileRepositoryInterface;
use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileSearchResponse;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use ReturnTypeWillChange;

class FileEloquentRepository implements FileRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly FileEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return FileModel::class;
    }

    protected function getTable(): string
    {
        return FileModel::TABLE;
    }

    protected function getEmptyCollection(): Collection
    {
        return new FileCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return FileSearchResponse
     */
    protected function getEmptySearchResponse(): FileSearchResponse
    {
        return new FileSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return FileSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new FileSearcher();
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): FileSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(FileEntity $dto): FileEntity
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->mapper->makeRepositoryDataFromEntity($dto, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?FileEntity
    {
        /** @var FileModel $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): FileSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    /**
     * @return int[]
     */
    public function getIdsByFullTextSearch(string $search): array
    {
        return FileModel::select('id')->whereRaw(
            sprintf("MATCH(%s) AGAINST(? IN BOOLEAN MODE)", FileModel::NAME),
            $search,
        )->get()->map(function (FileModel $file) {
            return $file->id;
        })->unique()->toArray();
    }
}
