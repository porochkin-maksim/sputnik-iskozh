<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Listeners;

use Core\Domains\Billing\Jobs\RecalcInvoiceJob;
use Core\Domains\Billing\Jobs\RecalcTransactionsPayedJob;
use Core\Domains\Billing\Transaction\Events\TransactionDeletedEvent;
use Core\Domains\Billing\TransactionToObject\TransactionToObjectLocator;

class TransactionDeletedListener
{
    public function handle(TransactionDeletedEvent $event): void
    {
        TransactionToObjectLocator::TransactionToObjectService()->drop($event->transaction);

        RecalcInvoiceJob::withChain([
            new RecalcTransactionsPayedJob($event->transaction->getInvoiceId())
        ])->dispatch($event->transaction->getInvoiceId());
    }
}
