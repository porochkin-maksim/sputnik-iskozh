<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Enums;

use Core\Shared\Enums\EnumCommonTrait;

enum TicketPriorityEnum: int
{
    use EnumCommonTrait;

    case LOW    = 1;
    case MEDIUM = 2;
    case HIGH   = 3;

    public function name(): string
    {
        return match ($this) {
            self::LOW    => 'Низкий',
            self::MEDIUM => 'Средний',
            self::HIGH   => 'Высокий',
        };
    }
}
