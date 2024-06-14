<?php

namespace Core\Domains\Access\Enums;

use Core\Enums\EnumCommonTrait;

enum RoleIdEnum: int
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
