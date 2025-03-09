<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Enums;

use Core\Enums\EnumCommonTrait;

enum HistoryType: int
{
    use EnumCommonTrait;

    case UNDEFINED   = 0;
    case SERVICE     = 1;
    case PERIOD      = 2;
    case ACCOUNT     = 3;
    case INVOICE     = 4;
    case TRANSACTION = 5;
    case PAYMENT     = 6;

    public function name(): string
    {
        return match ($this) {
            self::UNDEFINED   => '',
            self::SERVICE     => 'Услуга',
            self::PERIOD      => 'Период',
            self::ACCOUNT     => 'Участок',
            self::INVOICE     => 'Счёт',
            self::TRANSACTION => 'Транзакция',
            self::PAYMENT     => 'Платёж',
        };
    }
}
