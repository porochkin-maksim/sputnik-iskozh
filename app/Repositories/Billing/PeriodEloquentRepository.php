<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Period;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Repositories\RepositoryConfig;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodRepositoryInterface;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodSearchResponse;
use Core\Repositories\SearcherInterface;

class PeriodEloquentRepository implements PeriodRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly PeriodEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : Period::class,
            table              : Period::TABLE,
            collectionClass    : PeriodCollection::class,
            searchResponseClass: PeriodSearchResponse::class,
            searcherClass      : PeriodSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): PeriodSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(PeriodEntity $period): PeriodEntity
    {
        /** @var Period|null $model */
        $model = $this->getModelById($period->getId());
        /** @var Period $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($period, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?PeriodEntity
    {
        /** @var Period|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): PeriodSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }
}
