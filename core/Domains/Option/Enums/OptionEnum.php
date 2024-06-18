<?php declare(strict_types=1);

namespace Core\Domains\Option\Enums;

use Core\Enums\EnumCommonTrait;

/**
 * @method static tryFrom(int|string|null $value): ?static
 */
enum OptionEnum: int
{
    use EnumCommonTrait;

    case ELECTRIC_TARIFF        = 1;
    case ELECTRIC_SNT_TARIFF    = 2;
    case MEMBERSHIP_FEE         = 3;
    case GARBAGE_COLLECTION_FEE = 4;
    case ROAD_REPAIR_FEE        = 5;

    public function name(): string
    {
        return match ($this) {
            self::ELECTRIC_TARIFF        => 'Тариф электричество КВт/ч',
            self::ELECTRIC_SNT_TARIFF    => 'Тариф за электричество для СНТ КВт/ч',
            self::MEMBERSHIP_FEE         => 'Членский взнос за м2',
            self::GARBAGE_COLLECTION_FEE => 'Целевой сбор вывоз мусора',
            self::ROAD_REPAIR_FEE        => 'Целевой сбор ремонт дороги',
        };
    }

    public function type(): string
    {
        return match ($this) {
            self::ELECTRIC_TARIFF,
            self::MEMBERSHIP_FEE,
            self::ELECTRIC_SNT_TARIFF,
            self::GARBAGE_COLLECTION_FEE,
            self::ROAD_REPAIR_FEE => 'number',
        };
    }

    public function default(): int|float|string|null
    {
        return match ($this) {
            self::ELECTRIC_TARIFF,
            self::MEMBERSHIP_FEE,
            self::ELECTRIC_SNT_TARIFF,
            self::GARBAGE_COLLECTION_FEE,
            self::ROAD_REPAIR_FEE => 0,
        };
    }
}
