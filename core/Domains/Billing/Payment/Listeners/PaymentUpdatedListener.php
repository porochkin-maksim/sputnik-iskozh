<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Listeners;

use Core\Domains\Billing\Jobs\RecalcClaimsPaidJob;
use Core\Domains\Billing\Payment\Events\PaymentsUpdatedEvent;

class PaymentUpdatedListener
{
    public function handle(PaymentsUpdatedEvent $event): void
    {
        foreach ($event->getInvoiceIds() as $invoiceId) {
            dispatch_sync(new RecalcClaimsPaidJob($invoiceId));
        }
    }
}
