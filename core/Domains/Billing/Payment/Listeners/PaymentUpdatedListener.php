<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Listeners;

use Core\Domains\Billing\Jobs\RecalcClaimsPayedJob;
use Core\Domains\Billing\Payment\Events\PaymentsUpdatedEvent;
use lc;

class PaymentUpdatedListener
{
    public function handle(PaymentsUpdatedEvent $event): void
    {
        static $invoicesIds;
        if ( ! $invoicesIds) {
            $invoicesIds = [];
        }

        foreach ($event->getInvoiceIds() as $invoiceId) {
            if (in_array($invoiceId, $invoicesIds, true)) {
                continue;
            }

            $invoicesIds[] = $invoiceId;
            if (lc::isCli()) {
                dispatch_sync(new RecalcClaimsPayedJob($invoiceId));
            }
            else {
                dispatch(new RecalcClaimsPayedJob($invoiceId));
            }
        }
    }
}
