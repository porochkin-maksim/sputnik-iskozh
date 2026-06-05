<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Acquiring;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Acquiring\AcquiringCollection;
use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Contracts\AcquiringRepositoryInterface;
use Core\Domains\Billing\Acquiring\Models\AcquiringSearcher;
use Core\Domains\Billing\Acquiring\Models\AcquiringSearchResponse;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;

class AcquiringEloquentRepository implements AcquiringRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly AcquiringEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : Acquiring::class,
            table              : Acquiring::TABLE,
            collectionClass    : AcquiringCollection::class,
            searchResponseClass: AcquiringSearchResponse::class,
            searcherClass      : AcquiringSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
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
