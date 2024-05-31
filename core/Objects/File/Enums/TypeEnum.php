<?php

namespace Core\Objects\File\Enums;

/**
 * @method tryFrom(int|string|null $value): ?static
 */
enum TypeEnum: int
{
    case CONSTITUTION = 1;
    case REPORT       = 2;

    public function name(): string
    {
        return match ($this) {
            self::CONSTITUTION => 'Устав',
            self::REPORT       => 'Отчёт',
        };
    }
}
