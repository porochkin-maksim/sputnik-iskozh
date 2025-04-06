<?php declare(strict_types=1);

namespace Core\Domains\Option\Enums;

use Core\Enums\EnumCommonTrait;

/**
 * @method static tryFrom(int|string|null $value): ?static
 */
enum OptionEnum: int
{
    use EnumCommonTrait;

    case SNT_ACCOUNTING      = 1;
    case COUNTER_READING_DAY = 2;

    public function name(): string
    {
        return match ($this) {
            self::SNT_ACCOUNTING      => 'Реквизиты СНТ',
            self::COUNTER_READING_DAY => 'День снятия показаний счетчиков',
        };
    }
}
