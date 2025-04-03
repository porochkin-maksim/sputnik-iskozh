<?php declare(strict_types=1);

namespace Core\Domains\Counter\Listeners;

use Core\Domains\Counter\Events\CounterHistoryUpdatedEvent;
use Core\Domains\Counter\Jobs\RewatchCounterHistoryChainJob;

class CounterHistoryUpdatedListener
{
    public function handle(CounterHistoryUpdatedEvent $event): void
    {
        $counterId = $event->currentCounterHistory->getCounterId();

        if ( ! $counterId) {
            return;
        }

        if (
            $event->currentCounterHistory->getValue() !== $event->previousCounterHistory->getValue()
            || ! $event->currentCounterHistory->getDate()?->eq($event->previousCounterHistory->getDate())
        ) {
            dispatch(new RewatchCounterHistoryChainJob($counterId));
        }
    }
}
