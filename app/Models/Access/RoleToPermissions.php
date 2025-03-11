<?php declare(strict_types=1);

namespace App\Models\Access;

abstract class RoleToPermissions
{
    public const TABLE = 'roles_to_permissions';

    public const ROLE       = 'role';
    public const PERMISSION = 'permission';
}
