<?php declare(strict_types=1);

namespace Core\Domains\Access\Enums;

use Core\Domains\Access\Models\RoleDTO;

abstract class Permission
{
    public static function canEditNews(?RoleDTO $role): bool
    {
        if ($role?->is(RoleIdEnum::ADMIN)) {
            return true;
        }

        return false;
    }

    public static function canEditFiles(?RoleDTO $role): bool
    {
        if ($role?->is(RoleIdEnum::ADMIN)) {
            return true;
        }

        return false;
    }

    public static function canEditReports(?RoleDTO $role): bool
    {
        if ($role?->is(RoleIdEnum::ADMIN)) {
            return true;
        }

        return false;
    }

    public static function canEditOptions(?RoleDTO $role): bool
    {
        if ($role?->is(RoleIdEnum::ADMIN)) {
            return true;
        }

        return false;
    }
}
