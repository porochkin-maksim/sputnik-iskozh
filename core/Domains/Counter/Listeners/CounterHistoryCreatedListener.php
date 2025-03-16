<?php declare(strict_types=1);

namespace Core\Domains\Counter\Listeners;

use Core\Domains\Counter\Events\CounterHistoryCreatedEvent;
use Core\Domains\Counter\Jobs\NotifyAboutNewUnverifiedCounterHistoryJob;

class CounterHistoryCreatedListener
{
    public function handle(CounterHistoryCreatedEvent $event): void
    {
        NotifyAboutNewUnverifiedCounterHistoryJob::dispatch($event->counterHistoryId);
    }
}
