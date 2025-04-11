<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Enums;

use Core\Enums\EnumCommonTrait;

/**
 * @method static tryFrom(int|string|null $value): ?static
 */
enum ExDataTypeEnum: int
{
    use EnumCommonTrait;

    case USER    = 1;
    case ACCOUNT = 2;

    public function name(): string
    {
        return match ($this) {
            self::USER    => 'Пользователь',
            self::ACCOUNT => 'Участок',
        };
    }
}
