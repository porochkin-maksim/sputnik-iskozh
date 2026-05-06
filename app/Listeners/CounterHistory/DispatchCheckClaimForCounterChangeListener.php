<?php declare(strict_types=1);

namespace App\Listeners\CounterHistory;

use App\Jobs\Billing\CheckClaimForCounterChangeJob;
use Core\Domains\CounterHistory\Events\CounterHistoryConfirmed;

class DispatchCheckClaimForCounterChangeListener
{
    public function handle(CounterHistoryConfirmed $event): void
    {
        dispatch(new CheckClaimForCounterChangeJob($event->counterHistoryId));
    }
}
