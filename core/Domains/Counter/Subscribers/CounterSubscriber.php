<?php declare(strict_types=1);

namespace Core\Domains\Counter\Subscribers;

use Core\Domains\Counter\Events\CounterHistoryCreatedEvent;
use Core\Domains\Counter\Events\CounterHistoryDeletingEvent;
use Core\Domains\Counter\Events\CounterHistoryUpdatedEvent;
use Core\Domains\Counter\Listeners\CounterHistoryCreatedListener;
use Core\Domains\Counter\Listeners\CounterHistoryDeletingListener;
use Core\Domains\Counter\Listeners\CounterHistoryUpdatedListener;
use Illuminate\Events\Dispatcher;

class CounterSubscriber
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(CounterHistoryCreatedEvent::class, CounterHistoryCreatedListener::class);
        $events->listen(CounterHistoryUpdatedEvent::class, CounterHistoryUpdatedListener::class);
        $events->listen(CounterHistoryDeletingEvent::class, CounterHistoryDeletingListener::class);
    }
}
