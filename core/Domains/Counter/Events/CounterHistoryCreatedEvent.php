<?php declare(strict_types=1);

namespace Core\Domains\Counter\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CounterHistoryCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $counterHistoryId,
    )
    {
    }
}
