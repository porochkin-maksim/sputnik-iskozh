<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Service;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceRepositoryInterface;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceSearchResponse;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class ServiceEloquentRepository implements ServiceRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly ServiceEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return Service::class;
    }

    protected function getTable(): string
    {
        return Service::TABLE;
    }

    protected function getEmptyCollection(): Collection
    {
        return new ServiceCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return ServiceSearchResponse
     */
    protected function getEmptySearchResponse(): ServiceSearchResponse
    {
        return new ServiceSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return ServiceSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new ServiceSearcher();
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): ServiceSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id): ?ServiceEntity
    {
        /** @var Service|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): ServiceSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    public function save(ServiceEntity $service): ServiceEntity
    {
        /** @var Service|null $model */
        $model = $this->getModelById($service->getId());
        /** @var Service $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($service, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }
}
