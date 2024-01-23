<?php

namespace Core\Objects\Report\Enums;

use Core\Enums\ArrayNamesTrait;

/**
 * @method tryFrom(int|string|null $value): ?static
 */
enum TypeEnum: int
{
    use ArrayNamesTrait;

    case SINGLE    = 0;
    case WEEK      = 1;
    case TWO_WEEK  = 2;
    case MONTH     = 3;
    case QUARTER   = 4;
    case HALF_YEAR = 5;
    case YEAR      = 6;

    public function name(): string
    {
        return match ($this) {
            self::SINGLE    => 'Разовый',
            self::WEEK      => 'Недельный',
            self::TWO_WEEK  => 'Двунедельный',
            self::MONTH     => 'Месячный',
            self::QUARTER   => 'Квартальный',
            self::HALF_YEAR => 'Полугодовой',
            self::YEAR      => 'Годовой',
        };
    }
}
