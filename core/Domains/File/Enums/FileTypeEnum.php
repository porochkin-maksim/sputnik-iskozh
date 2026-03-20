<?php declare(strict_types=1);

namespace Core\Domains\File\Enums;

/**
 * @method tryFrom(int|string|null $value): ?static
 */
enum FileTypeEnum: int
{
    case CONSTITUTION     = 1;
    case NEWS             = 3;
    case PAYMENT          = 4;
    case COUNTER_HISTORY  = 5;
    case COUNTER_PASSPORT = 6;

    public function name(): string
    {
        return match ($this) {
            self::CONSTITUTION     => 'Устав',
            self::NEWS             => 'Новость',
            self::PAYMENT          => 'Платёж',
            self::COUNTER_HISTORY  => 'Показания счётчика',
            self::COUNTER_PASSPORT => 'Паспорт счётчика',
        };
    }
}
