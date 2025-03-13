<?php

namespace Core\Domains\File\Enums;

/**
 * @method tryFrom(int|string|null $value): ?static
 */
enum TypeEnum: int
{
    case CONSTITUTION = 1;
    case REPORT       = 2;
    case NEWS         = 3;
    case PAYMENT      = 4;

    public function name(): string
    {
        return match ($this) {
            self::CONSTITUTION => 'Устав',
            self::REPORT       => 'Отчёт',
            self::NEWS         => 'Новость',
            self::PAYMENT      => 'Платёж',
        };
    }
}
