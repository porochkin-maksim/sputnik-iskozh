<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\ClaimToObject;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectCollection;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectEntity;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectRepositoryInterface;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectSearcher;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectSearchResponse;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class ClaimToObjectEloquentRepository implements ClaimToObjectRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly ClaimToObjectEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return ClaimToObject::class;
    }

    protected function getTable(): string
    {
        return ClaimToObject::TABLE;
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new ClaimToObjectCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return ClaimToObjectSearchResponse
     */
    protected function getEmptySearchResponse(): ClaimToObjectSearchResponse
    {
        return new ClaimToObjectSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return ClaimToObjectSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new ClaimToObjectSearcher();
    }

    public function search(SearcherInterface $searcher): ClaimToObjectSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(ClaimToObjectEntity $claimToObject): ClaimToObjectEntity
    {
        /** @var ClaimToObject|null $model */
        $model = $this->getModelById($claimToObject->getId());
        /** @var ClaimToObject $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($claimToObject, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?ClaimToObjectEntity
    {
        /** @var ClaimToObject|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): ClaimToObjectSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    public function deleteByClaimId(int $claimId): int
    {
        return ClaimToObject::query()
            ->where(ClaimToObject::CLAIM_ID, $claimId)
            ->delete();
    }
}
