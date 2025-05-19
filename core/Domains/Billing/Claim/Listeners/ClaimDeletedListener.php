<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Listeners;

use Core\Domains\Billing\Jobs\RecalcClaimsPayedJob;
use Core\Domains\Billing\Claim\Events\ClaimDeletedEvent;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectLocator;

class ClaimDeletedListener
{
    public function handle(ClaimDeletedEvent $event): void
    {
        ClaimToObjectLocator::ClaimToObjectService()->drop($event->claim);

        $invoiceId = $event->claim->getInvoiceId();
        dispatch_sync(new RecalcClaimsPayedJob($invoiceId));
    }
}
