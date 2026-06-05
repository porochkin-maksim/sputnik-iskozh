<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

readonly class RewatchCounterHistoryChainInput
{
    public function __construct(
        public int $counterId,
    )
    {
    }
}
