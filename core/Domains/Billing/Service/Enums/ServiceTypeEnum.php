<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Enums;

use Core\Enums\EnumCommonTrait;

enum ServiceTypeEnum: int
{
    use EnumCommonTrait;

    case MEMBERSHIP_FEE  = 1;
    case ELECTRIC_TARIFF = 2;
    case TARGET_FEE      = 3;
    case OTHER           = 4;

    public function name(): string
    {
        return match ($this) {
            self::MEMBERSHIP_FEE  => 'Членский взнос',
            self::ELECTRIC_TARIFF => 'Электричество',
            self::TARGET_FEE      => 'Целевой сбор',
            self::OTHER           => 'Прочее',
        };
    }
}
