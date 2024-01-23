<?php

namespace Core\Queue;

enum QueueEnum: string
{
    case HIGH    = 'high';
    case EMAIL   = 'email';
    case DEFAULT = 'default';
    case LOW     = 'low';

    /** @return string[] */
    public static function values(): array
    {
        return array_map(fn(QueueEnum $enum) => $enum->value, self::cases());
    }

    /** @return string[] */
    public static function normalPriorityValues(): array
    {
        return array_map(fn(QueueEnum $enum) => $enum->value, [self::EMAIL, self::DEFAULT, self::LOW,]);
    }
}
