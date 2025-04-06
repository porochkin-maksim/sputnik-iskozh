<?php declare(strict_types=1);

namespace Core\Domains\Counter\Listeners;

use Core\Domains\Counter\Events\CounterHistoryCreatedEvent;
use Core\Domains\Counter\Jobs\NotifyAboutNewUnverifiedCounterHistoryJob;
use Core\Domains\Counter\Jobs\RewatchCounterHistoryChainJob;

class CounterHistoryCreatedListener
{
    /**
     * Нужно создать транзакцию на оплату
     * А так же уведомить администратора о новом показании счетчика
     */
    public function handle(CounterHistoryCreatedEvent $event): void
    {
        if ( ! $event->counterHistory?->isVerified()) {
            dispatch(new NotifyAboutNewUnverifiedCounterHistoryJob($event->counterHistory->getId()));
        }

        $counterId = $event->counterHistory->getCounterId();
        if ($counterId) {
            dispatch(new RewatchCounterHistoryChainJob($counterId));
        }
    }
}
