<?php

namespace Core\Domains\News\Enums;

use Core\Enums\EnumCommonTrait;

enum CategoryEnum: int
{
    use EnumCommonTrait;

    case DEFAULT      = 0;
    case NOTIFICATION = 1;

    public function name(): string
    {
        return match ($this) {
            self::DEFAULT      => 'Разное',
            self::NOTIFICATION => 'Объявление',
        };
    }
}
