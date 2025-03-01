<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Listeners;

use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\Events\InvoiceCreatedEvent;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Period\Events\PeriodCreatedEvent;
use Core\Domains\Billing\Period\Jobs\CreateMainServicesJob;
use Core\Domains\Billing\Transaction\Jobs\CreateTransactionsForIncomeInvoiceJob;

class PeriodCreatedListener
{
    public function handle(PeriodCreatedEvent $event): void
    {
        CreateMainServicesJob::dispatchSync($event->periodId);
    }
}
