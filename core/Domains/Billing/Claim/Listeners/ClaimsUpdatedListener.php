<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Listeners;

use Core\Domains\Billing\Jobs\RecalcInvoiceJob;
use Core\Domains\Billing\Jobs\RecalcClaimsPayedJob;
use Core\Domains\Billing\Claim\Events\ClaimsUpdatedEvent;

class ClaimsUpdatedListener
{
    public function handle(ClaimsUpdatedEvent $event): void
    {
        foreach ($event->getInvoiceIds() as $invoiceId) {
            RecalcInvoiceJob::withChain([new RecalcClaimsPayedJob($invoiceId)])->dispatch($invoiceId);
        }
    }
}
