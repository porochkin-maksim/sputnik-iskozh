<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Listeners;

use Core\Domains\Billing\Invoice\Events\InvoiceCreatedEvent;
use Core\Domains\Billing\Transaction\Jobs\CreateTransactionsForIncomeInvoiceJob;

class InvoiceCreatedListener
{
    public function handle(InvoiceCreatedEvent $event): void
    {
        foreach ($event->invoiceIds as $invoiceId) {
            CreateTransactionsForIncomeInvoiceJob::dispatch($invoiceId);
        }
    }
}
