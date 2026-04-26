<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Acquiring;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Billing\Acquiring\AcquiringCollection;
use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Contracts\AcquiringRepositoryInterface;
use Core\Domains\Billing\Acquiring\Models\AcquiringSearcher;
use Core\Domains\Billing\Acquiring\Models\AcquiringSearchResponse;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class AcquiringEloquentRepository implements AcquiringRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly AcquiringEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return Acquiring::class;
    }

    protected function getTable(): string
    {
        return Acquiring::TABLE;
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new AcquiringCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return AcquiringSearchResponse
     */
    protected function getEmptySearchResponse(): AcquiringSearchResponse
    {
        return new AcquiringSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return AcquiringSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new AcquiringSearcher();
    }

    public function search(SearcherInterface $searcher): AcquiringSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id): ?AcquiringEntity
    {
        /** @var Acquiring|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): AcquiringSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    public function save(AcquiringEntity $acquiring): AcquiringEntity
    {
        /** @var Acquiring|null $model */
        $model = $this->getModelById($acquiring->getId());
        /** @var Acquiring $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($acquiring, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }
}
