<?php declare(strict_types=1);

namespace Core\Domains\Account\Repositories;

use App\Models\Account\Account;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\Collections\Accounts;
use Core\Domains\Account\Models\AccountSearcher;
use Illuminate\Support\Facades\DB;

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

    public function getByIds(array $ids): Accounts
    {
        return new Accounts($this->traitGetByIds($ids));
    }

    public function save(Account $report): Account
    {
        $report->save();

        return $report;
    }

    /**
     * @return Account[]
     */
    public function search(AccountSearcher $searcher): array
    {
        $ids = DB::table(static::TABLE)
            ->pluck('id')
            ->toArray();

        return $this->traitGetByIds($ids, $searcher);
    }

    public function getByUserId(int $id): ?Account
    {
        $id = $this->accountToUserRepository->getAccountIdByUserId($id);

        return Account::select()->with('users')->where('id', SearcherInterface::EQUALS, $id)->first();
    }
}
