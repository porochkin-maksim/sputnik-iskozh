<?php

namespace Core\Domains\Account\Enums;

use Core\Enums\EnumCommonTrait;

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
