<?php declare(strict_types=1);

namespace Core\Domains\Account;

use Core\Shared\Enums\EnumCommonTrait;

enum AccountIdEnum: int
{
    use EnumCommonTrait;

    case SNT = 1;

    public function name(): string
    {
        return match ($this) {
            self::SNT => 'СНТ Спутник-Искож',
        };
    }
}
