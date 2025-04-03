<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Subscribers;

use Core\Domains\Billing\Transaction\Events\TransactionDeletedEvent;
use Core\Domains\Billing\Transaction\Events\TransactionsUpdatedEvent;
use Core\Domains\Billing\Transaction\Listeners\TransactionDeletedListener;
use Core\Domains\Billing\Transaction\Listeners\TransactionsUpdatedListener;
use Illuminate\Events\Dispatcher;

class TransactionSubscriber
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(TransactionsUpdatedEvent::class, TransactionsUpdatedListener::class);
        $events->listen(TransactionDeletedEvent::class, TransactionDeletedListener::class);
    }
}
