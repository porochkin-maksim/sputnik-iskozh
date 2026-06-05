<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Enums;

use Core\Shared\Enums\EnumCommonTrait;

enum StatusEnum: int
{
    use EnumCommonTrait;

    case NEW      = 1;
    case PROCESS  = 2;
    case CANCELED = 3;
    case PAID     = 4;

    public function name(): string
    {
        return match ($this) {
            self::NEW      => 'Новый',
            self::PROCESS  => 'В процессе',
            self::CANCELED => 'Отменён',
            self::PAID     => 'Оплачен',
        };
    }

    public function isNew(): bool
    {
        return $this === self::NEW;
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function isProcess(): bool
    {
        return $this === self::PROCESS;
    }

    public function isCanceled(): bool
    {
        return $this === self::CANCELED;
    }
}
