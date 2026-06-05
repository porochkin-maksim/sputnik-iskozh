<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Access\Role;
use App\Models\Access\RoleToPermissions;
use App\Models\User;
use Core\Domains\Access\PermissionEnum;

trait WithAdminAccess
{
    private function createAdminUser(): User
    {
        $role = Role::factory()->create(['name' => 'admin']);

        $permissions = [
            PermissionEnum::ROLES_VIEW,
            PermissionEnum::USERS_VIEW,
            PermissionEnum::OPTIONS_VIEW,
            PermissionEnum::ACCOUNTS_VIEW,
            PermissionEnum::ACCOUNTS_EDIT,
            PermissionEnum::ACCOUNTS_DROP,
            PermissionEnum::PERIODS_VIEW,
            PermissionEnum::PERIODS_EDIT,
            PermissionEnum::PERIODS_DROP,
            PermissionEnum::SERVICES_VIEW,
            PermissionEnum::SERVICES_EDIT,
            PermissionEnum::SERVICES_DROP,
            PermissionEnum::INVOICES_VIEW,
            PermissionEnum::INVOICES_EDIT,
            PermissionEnum::INVOICES_DROP,
            PermissionEnum::PAYMENTS_VIEW,
            PermissionEnum::COUNTERS_VIEW,
            PermissionEnum::HELP_DESK_VIEW,
            PermissionEnum::HELP_DESK_EDIT,
            PermissionEnum::HELP_DESK_DROP,
            PermissionEnum::NEWS_VIEW,
            PermissionEnum::FILES_VIEW,
        ];

        foreach ($permissions as $permission) {
            RoleToPermissions::create([
                RoleToPermissions::ROLE       => $role->id,
                RoleToPermissions::PERMISSION => $permission->value,
            ]);
        }

        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        return $user;
    }
}
