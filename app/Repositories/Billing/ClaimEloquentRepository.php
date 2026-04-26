<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Claim;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimRepositoryInterface;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimSearchResponse;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class ClaimEloquentRepository implements ClaimRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly ClaimEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return Claim::class;
    }

    protected function getTable(): string
    {
        return Claim::TABLE;
    }

    protected function getEmptyCollection(): Collection
    {
        return new ClaimCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return ClaimSearchResponse
     */
    protected function getEmptySearchResponse(): ClaimSearchResponse
    {
        return new ClaimSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return ClaimSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new ClaimSearcher();
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): ClaimSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id): ?ClaimEntity
    {
        /** @var Claim|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): ClaimSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    public function save(ClaimEntity $claim): ClaimEntity
    {
        /** @var Claim|null $model */
        $model = $this->getModelById($claim->getId());
        /** @var Claim $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($claim, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }
}
