<?php declare(strict_types=1);

namespace Core\Domains\Access;

use Core\Domains\User\UserIdEnum;

abstract class UserToRole
{
    private const array UserToRole = [
        UserIdEnum::OWNER => RoleEnum::ADMIN,
    ];

    public static function getForUser(?int $id): ?RoleEnum
    {
        return self::UserToRole[$id] ?? null;
    }
}
