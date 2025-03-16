<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Enums;

use Core\Enums\EnumCommonTrait;

enum InvoiceTypeEnum: int
{
    use EnumCommonTrait;

    case REGULAR = 1;
    case INCOME  = 2;
    case OUTCOME = 3;

    public function name(): string
    {
        return match ($this) {
            self::REGULAR => 'Регулярный',
            self::INCOME  => 'Доход СНТ',
            self::OUTCOME => 'Расход СНТ',
        };
    }
}
