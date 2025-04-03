<?php declare(strict_types=1);

namespace Core\Domains\Option\Enums;

use Core\Enums\EnumCommonTrait;

/**
 * @method static tryFrom(int|string|null $value): ?static
 */
enum OptionEnum: int
{
    use EnumCommonTrait;

    public function name(): string
    {
        return '';
    }

    public function type(): string
    {
        return 'number';
    }

    public function default(): int|float|string|null
    {
        return 0;
    }
}
