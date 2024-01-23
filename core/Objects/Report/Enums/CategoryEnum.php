<?php

namespace Core\Objects\Report\Enums;

use Core\Enums\ArrayNamesTrait;

/**
 * @method tryFrom(int|string|null $value): ?static
 */
enum CategoryEnum: int
{
    use ArrayNamesTrait;

    case UNDEFINED = 1;
    case GARBAGE   = 2;

    public function name(): string
    {
        return match ($this) {
            self::UNDEFINED => 'Без категории',
            self::GARBAGE   => 'Мусор',
        };
    }
}
