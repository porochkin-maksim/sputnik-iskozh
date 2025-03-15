<?php declare(strict_types=1);

namespace Core\Domains\Account\Repositories;

use App\Models\Account\Account;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\Collections\AccountCollection;

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

    public function getByIds(array $ids): AccountCollection
    {
        return new AccountCollection($this->traitGetByIds($ids));
    }

    public function save(Account $object): Account
    {
        $object->save();

        return $object;
    }

    public function getByUserId(int $id): ?Account
    {
        $id = $this->accountToUserRepository->getAccountIdByUserId($id);

        return Account::select()->with('users')->where('id', SearcherInterface::EQUALS, $id)->first();
    }
}
