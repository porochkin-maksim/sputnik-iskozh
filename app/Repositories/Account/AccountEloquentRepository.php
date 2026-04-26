<?php declare(strict_types=1);

namespace App\Repositories\Account;

use App\Models\Account\Account;
use App\Models\Account\AccountToUser;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountRepositoryInterface;
use Core\Domains\Account\AccountSearchResponse;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use Illuminate\Support\Facades\DB;
use ReturnTypeWillChange;

class AccountEloquentRepository implements AccountRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly AccountEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return Account::class;
    }

    protected function getTable(): string
    {
        return Account::TABLE;
    }

    protected function getEmptyCollection(): Collection
    {
        return new AccountCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return AccountSearchResponse
     */
    protected function getEmptySearchResponse(): AccountSearchResponse
    {
        return new AccountSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return AccountSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new AccountSearcher();
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): AccountSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id): ?AccountEntity
    {
        /** @var ?Account $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): AccountCollection
    {
        return $this->search((new AccountSearcher())->setIds($ids))->getItems();
    }

    public function save(AccountEntity $entity): AccountEntity
    {
        /** @var ?Account $model */
        $model = $this->getModelById($entity->getId());
        /** @var Account $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($entity, $model);
        $model->save();

        if ($entity->hasUsers()) {
            $model->users()->sync($entity->getUsers()->getIds());
        }

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getByUserId(int $id): AccountCollection
    {
        $ids = DB::table(AccountToUser::TABLE)
            ->where(AccountToUser::USER, $id)
            ->groupBy(AccountToUser::ACCOUNT)
            ->pluck(AccountToUser::ACCOUNT)
            ->toArray()
        ;

        if ($ids === []) {
            return new AccountCollection();
        }

        return $this->search(
            $this->getEmptySearcher()
                ->setIds($ids)
                ->setWithUsers(),
        )->getItems();
    }
}
