<?php declare(strict_types=1);

namespace Core\Domains\News;

use Core\Shared\Enums\EnumCommonTrait;

enum NewsCategoryEnum: int
{
    use EnumCommonTrait;

    case DEFAULT      = 0;
    case ANNOUNCEMENT = 1;

    public function name(): string
    {
        return match ($this) {
            self::DEFAULT      => 'Разное',
            self::ANNOUNCEMENT => 'Объявление',
        };
    }
}
