<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Enums;

use Core\Enums\EnumCommonTrait;

enum Event: int
{
    use EnumCommonTrait;

    case COMMON = 0;
    case CREATE = 1;
    case UPDATE = 2;
    case DELETE = 3;

    public function name(): string
    {
        return match ($this) {
            self::COMMON => '',
            self::CREATE => 'Создание',
            self::UPDATE => 'Обновление',
            self::DELETE => 'Удаление',
        };
    }
}
