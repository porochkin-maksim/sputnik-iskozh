<?php declare(strict_types=1);

namespace Core\Domains\Counter\Subscribers;

use Core\Domains\Counter\Events\CounterHistoryCreatedEvent;
use Core\Domains\Counter\Listeners\CounterHistoryCreatedListener;
use Illuminate\Events\Dispatcher;

class CounterSubscriber
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(CounterHistoryCreatedEvent::class, CounterHistoryCreatedListener::class);
    }
}
