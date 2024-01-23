<?php

namespace Core\Objects\File\Enums;

/**
 * @method tryFrom(int|string|null $value): ?static
 */
enum CategoryTypeEnum: int
{
    case COMMON      = 1;
    case INCOME      = 2;
    case ELECTRICITY = 3;

    public function name(): string
    {
        return match ($this) {
            self::COMMON      => 'Общий отчёт',
            self::INCOME      => 'Пополнения',
            self::ELECTRICITY => 'Электричество',
        };
    }
}
