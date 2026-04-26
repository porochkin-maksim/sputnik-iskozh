<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service;

use Core\Shared\Enums\EnumCommonTrait;

enum ServiceTypeEnum: int
{
    use EnumCommonTrait;

    case MEMBERSHIP_FEE = 1;
    case ELECTRIC_TARIFF = 2;
    case TARGET_FEE = 3;
    case OTHER = 4;
    case DEBT = 5;
    case ADVANCE_PAYMENT = 6;
    case PERSONAL_FEE = 7;

    public function name(): string
    {
        return match ($this) {
            self::MEMBERSHIP_FEE => 'Членский взнос',
            self::ELECTRIC_TARIFF => 'Электричество',
            self::TARGET_FEE => 'Целевой сбор',
            self::OTHER => 'Прочее',
            self::DEBT => 'Долг',
            self::ADVANCE_PAYMENT => 'Аванс',
            self::PERSONAL_FEE => 'Персональный взнос',
        };
    }

    public function isAdvance(): bool
    {
        return $this === self::ADVANCE_PAYMENT;
    }

    public function isDebt(): bool
    {
        return $this === self::DEBT;
    }
}
