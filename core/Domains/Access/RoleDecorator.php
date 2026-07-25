<?php declare(strict_types=1);

namespace Core\Domains\Access;

use Core\Domains\Access\PermissionEnum;

class RoleDecorator
{
    public function __construct(
        private readonly ?RoleEntity $role,
    )
    {
    }

    public function getFrontendPermissions(): array
    {
        $result          = [];
        $userPermissions = $this->role->getPermissions();

        foreach (PermissionEnum::cases() as $permission) {
            $section = $permission->sectionKey();
            $action  = $permission->actionKey();
            if (in_array($permission, $userPermissions)) {
                $result[$section][$action] = true;
            }
        }

        return $result;
    }

    public function isSuperAdmin(): bool
    {
        return $this->can(...PermissionEnum::cases());
    }

    public function canAccessAdmin(): bool
    {
        return $this->can(PermissionEnum::ADMIN_ACCESS);
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

    public function canAccessTreasury(): bool
    {
        return $this->can(PermissionEnum::TREASURY_ACCESS);
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
