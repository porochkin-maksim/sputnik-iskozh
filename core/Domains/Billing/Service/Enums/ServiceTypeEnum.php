<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Enums;

use Core\Enums\EnumCommonTrait;

enum ServiceTypeEnum: int
{
    use EnumCommonTrait;

    case MEMBERSHIP_FEE  = 1; // Членский взнос
    case ELECTRIC_TARIFF = 2; // Электричество
    case TARGET_FEE      = 3; // Целевой сбор
    case OTHER           = 4; // Прочее
    case DEBT            = 5; // Долг
    case ADVANCE_PAYMENT = 6; // Аванс

    public function name(): string
    {
        return match ($this) {
            self::MEMBERSHIP_FEE  => 'Членский взнос',
            self::ELECTRIC_TARIFF => 'Электричество',
            self::TARGET_FEE      => 'Целевой сбор',
            self::OTHER           => 'Прочее',
            self::DEBT            => 'Долг',
            self::ADVANCE_PAYMENT => 'Аванс',
        };
    }
}
