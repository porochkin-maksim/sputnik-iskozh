<?php

namespace Core\Domains\Report\Enums;

use Core\Enums\EnumCommonTrait;

/**
 * @method tryFrom(int|string|null $value): ?static
 */
enum CategoryEnum: int
{
    use EnumCommonTrait;

    case UNDEFINED = 0;
    case GARBAGE   = 1;

    public function name(): string
    {
        return match ($this) {
            self::UNDEFINED => 'Без категории',
            self::GARBAGE   => 'Мусор',
        };
    }

    public function defaultName(): ?string
    {
        return match ($this) {
            self::UNDEFINED => null,
            self::GARBAGE   => 'Вывоз мусора',
        };
    }
}
