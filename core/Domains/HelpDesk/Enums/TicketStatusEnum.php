<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Enums;

use Core\Shared\Enums\EnumCommonTrait;

enum TicketStatusEnum: int
{
    use EnumCommonTrait;

    case NEW                  = 1;
    case IN_PROGRESS          = 2;
    case WAITING_FOR_CUSTOMER = 3;
    case CLOSED               = 4;
    case REJECTED             = 5;

    public function name(): string
    {
        return match ($this) {
            self::NEW                  => 'Новая',
            self::IN_PROGRESS          => 'В работе',
            self::WAITING_FOR_CUSTOMER => 'Ожидает ответа',
            self::CLOSED               => 'Закрыта',
            self::REJECTED             => 'Отклонена',
        };
    }

    public function isNew(): bool
    {
        return $this === self::NEW;
    }

    public function isClosed(): bool
    {
        return $this === self::CLOSED;
    }

    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }
}
