<?php declare(strict_types=1);

namespace Core\Domains\Access\Enums;

use Core\Domains\Access\Models\RoleDTO;

abstract class Permission
{
    public static function canEditNews(?RoleDTO $role): bool
    {
        return $role?->is(RoleIdEnum::ADMIN);
    }

    public static function canEditFiles(?RoleDTO $role): bool
    {
        return $role?->is(RoleIdEnum::ADMIN);
    }

    public static function canEditReports(?RoleDTO $role): bool
    {
        return $role?->is(RoleIdEnum::ADMIN);
    }

    public static function canEditOptions(?RoleDTO $role): bool
    {
        return $role?->is(RoleIdEnum::ADMIN);
    }

    public static function canEditTemplates(?RoleDTO $role): bool
    {
        return $role?->is(RoleIdEnum::ADMIN);
    }
}
