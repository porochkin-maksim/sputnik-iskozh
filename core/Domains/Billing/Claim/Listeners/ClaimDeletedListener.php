<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Listeners;

use Core\Domains\Billing\Jobs\RecalcClaimsPayedJob;
use Core\Domains\Billing\Claim\Events\ClaimDeletedEvent;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectLocator;
use lc;

class ClaimDeletedListener
{
    public function handle(ClaimDeletedEvent $event): void
    {
        static $invoicesIds;
        if ( ! $invoicesIds) {
            $invoicesIds = [];
        }

        if (in_array($event->claim->getInvoiceId(), $invoicesIds, true)) {
            return;
        }
        $invoicesIds[] = $event->claim->getInvoiceId();

        ClaimToObjectLocator::ClaimToObjectService()->drop($event->claim);

        $invoiceId = $event->claim->getInvoiceId();
        if (lc::isCli()) {
            dispatch_sync(new RecalcClaimsPayedJob($invoiceId));
        }
        else {
            dispatch(new RecalcClaimsPayedJob($invoiceId));
        }
    }
}
