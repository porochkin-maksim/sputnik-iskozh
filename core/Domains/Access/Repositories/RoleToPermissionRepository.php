<?php declare(strict_types=1);

namespace Core\Domains\Access\Repositories;

use App\Models\Access\RoleToPermissions;
use Core\Db\Searcher\SearcherInterface;
use Illuminate\Support\Facades\DB;

class RoleToPermissionRepository
{
    /**
     * @return int[]
     */
    public function getPermissionsByRoleId(int $id): array
    {
        return DB::table(RoleToPermissions::TABLE)
            ->select(RoleToPermissions::PERMISSION)
            ->where(RoleToPermissions::ROLE, SearcherInterface::EQUALS, $id)
            ->pluck(RoleToPermissions::PERMISSION)
            ->toArray()
        ;
    }

    public function saveRolesPermissions(int $id, array $permissions): void
    {
        DB::table(RoleToPermissions::TABLE)->where(RoleToPermissions::ROLE, $id)->delete();
        DB::table(RoleToPermissions::TABLE)->insert(array_map(static function (string $permission) use ($id) {
            return [
                RoleToPermissions::ROLE       => $id,
                RoleToPermissions::PERMISSION => $permission,
            ];
        }, $permissions));
    }

    public function getPermissionsByRoleIds(array $getIds): array
    {
        $result = DB::table(RoleToPermissions::TABLE)
            ->select([RoleToPermissions::ROLE, RoleToPermissions::PERMISSION])
            ->whereIn(RoleToPermissions::ROLE, $getIds)
            ->get()
            ->groupBy('role');

        return $result->map(static function ($items) {
            return $items->map(static function ($item) { return $item->permission; })->toArray();
        })->toArray();
    }
}
