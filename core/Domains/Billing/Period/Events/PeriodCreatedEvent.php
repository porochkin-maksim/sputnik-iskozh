<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PeriodCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $periodId,
    )
    {
    }
}
