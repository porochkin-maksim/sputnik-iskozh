<?php declare(strict_types=1);

namespace Core\Domains\Access\Repositories;

use App\Models\Access\RoleToUser;
use Core\Db\Searcher\SearcherInterface;
use Illuminate\Support\Facades\DB;

class RoleToUserRepository
{
    public function getRoleIdByUserId(int $id): ?int
    {
        return DB::table(RoleToUser::TABLE)
            ->select(RoleToUser::ROLE)
            ->where(RoleToUser::USER, SearcherInterface::EQUALS, $id)
            ->groupBy(RoleToUser::ROLE)
            ->pluck(RoleToUser::ROLE)
            ->first();
    }

    /**
     * @return int[]
     */
    public function getUserIdsByRoleId(int $id): array
    {
        return DB::table(RoleToUser::TABLE)
            ->select(RoleToUser::USER)
            ->where(RoleToUser::ROLE, SearcherInterface::EQUALS, $id)
            ->groupBy(RoleToUser::USER)
            ->pluck(RoleToUser::USER)
            ->toArray();
    }
}
