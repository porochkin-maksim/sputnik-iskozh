<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Listeners;

use Core\Domains\Billing\Invoice\Jobs\RecalcInvoiceJob;
use Core\Domains\Billing\Transaction\Events\TransactionsUpdatedEvent;

class TransactionsUpdatedListener
{
    public function handle(TransactionsUpdatedEvent $event): void
    {
        foreach ($event->getInvoiceIds() as $invoiceId) {
            RecalcInvoiceJob::dispatch($invoiceId);
        }
    }
}
