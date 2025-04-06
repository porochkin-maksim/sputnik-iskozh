<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Enums;

use Core\Enums\EnumCommonTrait;

enum HistoryType: int
{
    use EnumCommonTrait;

    case UNDEFINED       = 0;
    case SERVICE         = 1;
    case PERIOD          = 2;
    case ACCOUNT         = 3;
    case INVOICE         = 4;
    case CLAIM           = 5;
    case PAYMENT         = 6;
    case USER            = 7;
    case ROLE            = 8;
    case COUNTER         = 9;
    case COUNTER_HISTORY = 10;
    case OPTION          = 11;

    public function name(): string
    {
        return match ($this) {
            self::UNDEFINED       => '',
            self::SERVICE         => 'Услуга',
            self::PERIOD          => 'Период',
            self::ACCOUNT         => 'Участок',
            self::INVOICE         => 'Счёт',
            self::CLAIM           => 'Услуга счёта',
            self::PAYMENT         => 'Платёж',
            self::USER            => 'Пользователь',
            self::ROLE            => 'Роль',
            self::COUNTER         => 'Счётчик',
            self::COUNTER_HISTORY => 'Показание счётчика',
            self::OPTION          => 'Опция',
        };
    }
}
