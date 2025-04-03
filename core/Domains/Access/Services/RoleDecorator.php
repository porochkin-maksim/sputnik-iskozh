<?php declare(strict_types=1);

namespace Core\Domains\Access\Services;

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\Models\RoleDTO;

class RoleDecorator
{
    public function __construct(
        private readonly ?RoleDTO $role,
    )
    {
    }

    public function isSuperAdmin(): bool
    {
        return $this->can(...PermissionEnum::cases());
    }

    public function canAccessAdmin(): bool
    {
        return $this->canAny(
            PermissionEnum::ROLES_VIEW,
            PermissionEnum::USERS_VIEW,
            PermissionEnum::ACCOUNTS_VIEW,
            PermissionEnum::PERIODS_VIEW,
            PermissionEnum::SERVICES_VIEW,
            PermissionEnum::INVOICES_VIEW,
            PermissionEnum::PAYMENTS_VIEW,
            PermissionEnum::COUNTERS_VIEW,
        );
    }

    public function can(PermissionEnum ...$permissions): bool
    {
        $result = true;
        foreach ($permissions as $permission) {
            if ( ! $result) {
                break;
            }
            $result = $this->role->hasPermission($permission);
        }

        return $result;
    }

    public function canAny(PermissionEnum ...$permissions): bool
    {
        $result = false;
        foreach ($permissions as $permission) {
            if ($result) {
                break;
            }
            $result = $this->role->hasPermission($permission);
        }

        return $result;
    }

    public function canNews(): bool
    {
        $actions = [
            PermissionEnum::NEWS_VIEW,
            PermissionEnum::NEWS_EDIT,
            PermissionEnum::NEWS_DROP,
        ];

        return $this->can(...$actions);
    }

    public function canFiles(): bool
    {
        $actions = [
            PermissionEnum::FILES_VIEW,
            PermissionEnum::FILES_EDIT,
            PermissionEnum::FILES_DROP,
        ];

        return $this->can(...$actions);
    }
}
