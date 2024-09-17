<?php declare(strict_types=1);

namespace Core\Domains\Poll\Enums;

use Core\Enums\EnumCommonTrait;

enum QuestionType: int
{
    use EnumCommonTrait;

    case TEXT            = 1;
    case CHECK           = 2;
    case CHOOSE          = 3;
    case MULTIPLE_CHOOSE = 4;

    public function name(): string
    {
        return match ($this) {
            self::TEXT            => 'Текстовый ответ',
            self::CHECK           => 'Простой выбор ',
            self::CHOOSE          => 'Один из списка',
            self::MULTIPLE_CHOOSE => 'Несколько из списка',
        };
    }
}
