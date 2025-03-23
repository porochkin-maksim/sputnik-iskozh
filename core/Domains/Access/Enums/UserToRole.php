<?php declare(strict_types=1);

namespace Core\Domains\Access\Enums;

use Core\Domains\User\Enums\UserIdEnum;

abstract class UserToRole
{
    private const UserToRole = [
        UserIdEnum::OWNER => RoleEnum::ADMIN,
    ];

    public static function getForUser(?int $id): ?RoleEnum
    {
        return self::UserToRole[$id] ?? null;
    }
}
