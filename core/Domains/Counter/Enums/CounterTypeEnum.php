<?php declare(strict_types=1);

namespace Core\Domains\Counter\Enums;

use Core\Enums\EnumCommonTrait;

/**
 * @method static tryFrom(int|string|null $value): ?static
 */
enum CounterTypeEnum: int
{
    use EnumCommonTrait;

    case ELECTRICITY = 1;

    public function name(): string
    {
        return match ($this) {
            self::ELECTRICITY => 'Электрический счётчик',
        };
    }
}
