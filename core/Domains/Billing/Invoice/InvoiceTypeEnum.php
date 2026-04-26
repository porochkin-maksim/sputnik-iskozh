<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice;

use Core\Shared\Enums\EnumCommonTrait;

enum InvoiceTypeEnum: int
{
    use EnumCommonTrait;

    case REGULAR = 1;
    case INCOME = 2;
    case OUTCOME = 3;

    public function name(): string
    {
        return match ($this) {
            self::REGULAR => 'Регулярный доход',
            self::INCOME => 'Доход',
            self::OUTCOME => 'Расход',
        };
    }
}
