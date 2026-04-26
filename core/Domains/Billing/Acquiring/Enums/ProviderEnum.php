<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Enums;

use Core\Shared\Enums\EnumCommonTrait;

enum ProviderEnum: int
{
    use EnumCommonTrait;

    case VTB = 1;

    public function name(): string
    {
        return match ($this) {
            self::VTB => 'ВТБ',
        };
    }
}
