<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory\Events;

readonly class CounterHistoriesLinked
{
    public function __construct(
        public int $counterHistoryId,
    )
    {
    }
}
