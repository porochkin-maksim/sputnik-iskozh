<?php declare(strict_types=1);

namespace Core\Domains\Access\Enums;

use Core\Domains\User\Enums\UserIdEnum;

abstract class UserToRole
{
    private const UserToRole = [
        UserIdEnum::OWNER => RoleIdEnum::ADMIN,
    ];

    public static function getForUser(?int $id): ?RoleIdEnum
    {
        return self::UserToRole[$id] ?? null;
    }
}
