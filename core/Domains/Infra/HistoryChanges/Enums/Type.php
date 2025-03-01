<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Enums;

use Core\Domains\Account\Models\AccountComparator;
use Core\Domains\Billing\Period\Models\PeriodComparator;
use Core\Domains\Billing\Service\Models\ServiceComparator;
use Core\Enums\EnumCommonTrait;

enum Type: int
{
    use EnumCommonTrait;

    case UNDEFINED = 0;
    case SERVICE   = 1;
    case PERIOD    = 2;
    case ACCOUNT   = 3;

    public static function makeTypeByClass(string $class): self
    {
        return match ($class) {
            AccountComparator::class => self::ACCOUNT,
            PeriodComparator::class  => self::PERIOD,
            ServiceComparator::class => self::SERVICE,
            default                  => self::UNDEFINED,
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::UNDEFINED => '',
            self::SERVICE   => 'Услуга',
            self::PERIOD    => 'Период',
            self::ACCOUNT   => 'Участок',
        };
    }
}
