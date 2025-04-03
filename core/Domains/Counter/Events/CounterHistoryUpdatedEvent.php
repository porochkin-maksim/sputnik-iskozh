<?php declare(strict_types=1);

namespace Core\Domains\Counter\Events;

use Core\Domains\Counter\Models\CounterHistoryDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CounterHistoryUpdatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public CounterHistoryDTO $currentCounterHistory,
        public CounterHistoryDTO $previousCounterHistory,
    )
    {
    }
}
