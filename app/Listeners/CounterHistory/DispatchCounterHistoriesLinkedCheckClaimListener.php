<?php declare(strict_types=1);

namespace App\Listeners\CounterHistory;

use App\Jobs\Billing\CheckClaimForCounterChangeJob;
use Core\Domains\CounterHistory\Events\CounterHistoriesLinked;

class DispatchCounterHistoriesLinkedCheckClaimListener
{
    public function handle(CounterHistoriesLinked $event): void
    {
        dispatch(new CheckClaimForCounterChangeJob($event->counterHistoryId));
    }
}
