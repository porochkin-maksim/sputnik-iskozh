<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Listeners;

use Core\Domains\Billing\Jobs\RecalcClaimsPayedJob;
use Core\Domains\Billing\Claim\Events\ClaimsUpdatedEvent;
use lc;

class ClaimsUpdatedListener
{
    public function handle(ClaimsUpdatedEvent $event): void
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
