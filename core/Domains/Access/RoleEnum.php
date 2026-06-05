<?php

namespace Core\Domains\Access;

use Core\Shared\Enums\EnumCommonTrait;

enum RoleEnum: int
{
    use EnumCommonTrait;

    case ADMIN = 1;

    public function name(): string
    {
        return match ($this) {
            self::ADMIN => 'Администратор',
        };
    }
}
