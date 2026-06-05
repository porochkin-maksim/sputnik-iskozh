<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\ClaimToObject;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectCollection;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectEntity;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectRepositoryInterface;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectSearcher;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectSearchResponse;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;

class ClaimToObjectEloquentRepository implements ClaimToObjectRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly ClaimToObjectEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : ClaimToObject::class,
            table              : ClaimToObject::TABLE,
            collectionClass    : ClaimToObjectCollection::class,
            searchResponseClass: ClaimToObjectSearchResponse::class,
            searcherClass      : ClaimToObjectSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
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
