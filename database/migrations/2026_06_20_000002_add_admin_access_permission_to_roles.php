<?php declare(strict_types=1);

use App\Models\Access\RoleToPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roleIds = DB::table('roles')->pluck('id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table(RoleToPermissions::TABLE)
                ->where(RoleToPermissions::ROLE, $roleId)
                ->where(RoleToPermissions::PERMISSION, 0)
                ->exists()
            ;

            if ( ! $exists) {
                DB::table(RoleToPermissions::TABLE)->insert([
                    RoleToPermissions::ROLE       => $roleId,
                    RoleToPermissions::PERMISSION => 0,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table(RoleToPermissions::TABLE)
            ->where(RoleToPermissions::PERMISSION, 0)
            ->delete()
        ;
    }
};
