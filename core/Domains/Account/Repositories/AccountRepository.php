<?php declare(strict_types=1);

namespace Core\Domains\Account\Repositories;

use App\Models\Account\Account;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\Collections\AccountCollection;
use Illuminate\Database\Eloquent\Collection;

class AccountRepository
{
    private const TABLE = Account::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    public function __construct(
        private AccountToUserRepository $accountToUserRepository
    )
    {
    }

    protected function modelClass(): string
    {
        return Account::class;
    }

    public function getById(?int $id): ?Account
    {
        /** @var ?Account $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function save(Account $object): Account
    {
        $object->save();

        return $object;
    }

    /**
     * @param int $id
     *
     * @return Collection|Account[]
     */
    public function getByUserId(int $id): Collection|array
    {
        $ids = $this->accountToUserRepository->getAccountsIdsByUserId($id);

        return Account::select()->with(Account::USERS)->whereIn(Account::ID, $ids)->get();
    }
}
