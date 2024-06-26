<?php declare(strict_types=1);

namespace Core\Domains\Account\Repositories;

use App\Models\Account\AccountToUser;
use Core\Db\Searcher\SearcherInterface;
use Illuminate\Support\Facades\DB;

class AccountToUserRepository
{
    public function getAccountIdByUserId(int $id): ?int
    {
        return DB::table(AccountToUser::TABLE)
            ->select(AccountToUser::ACCOUNT)
            ->where(AccountToUser::USER, SearcherInterface::EQUALS, $id)
            ->groupBy(AccountToUser::ACCOUNT)
            ->pluck(AccountToUser::ACCOUNT)
            ->first();
    }

    /**
     * @return int[]
     */
    public function getUserIdsByAccountId(int $id): array
    {
        return DB::table(AccountToUser::TABLE)
            ->select(AccountToUser::USER)
            ->where(AccountToUser::ACCOUNT, SearcherInterface::EQUALS, $id)
            ->groupBy(AccountToUser::USER)
            ->pluck(AccountToUser::USER)
            ->toArray();
    }
}
