<?php

namespace Core\Domains\News\Enums;

use Core\Enums\EnumCommonTrait;

/**
 * @method tryFrom(int|string|null $value): ?static
 */
enum TypeEnum: int
{
    use EnumCommonTrait;
}
