<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Subscribers;

use Core\Domains\Billing\Period\Events\PeriodCreatedEvent;
use Core\Domains\Billing\Period\Listeners\PeriodCreatedListener;
use Illuminate\Events\Dispatcher;

class PeriodSubscriber
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(PeriodCreatedEvent::class, PeriodCreatedListener::class);
    }
}
