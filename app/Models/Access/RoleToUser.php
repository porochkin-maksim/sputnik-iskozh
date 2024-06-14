<?php declare(strict_types=1);

namespace App\Models\Access;

abstract class RoleToUser
{
    public const TABLE = 'roles_to_users';

    public const ROLE = 'role';
    public const USER = 'user';
}
