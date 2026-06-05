<?php declare(strict_types=1);

namespace App\Models\Access;

abstract class RoleToUser
{
    public const string TABLE = 'roles_to_users';

    public const string ROLE = 'role';
    public const string USER = 'user';
}
