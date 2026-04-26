<?php declare(strict_types=1);

namespace App\Repositories\Option;

use App\Models\Infra\Option;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Option\OptionCollection;
use Core\Domains\Option\OptionEntity;
use Core\Domains\Option\OptionRepositoryInterface;
use Core\Domains\Option\OptionSearcher;
use Core\Domains\Option\OptionSearchResponse;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class OptionEloquentRepository implements OptionRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly OptionEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return Option::class;
    }

    protected function getTable(): string
    {
        return Option::TABLE;
    }

    protected function getEmptyCollection(): Collection
    {
        return new OptionCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return OptionSearchResponse
     */
    protected function getEmptySearchResponse(): OptionSearchResponse
    {
        return new OptionSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return OptionSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new OptionSearcher();
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
