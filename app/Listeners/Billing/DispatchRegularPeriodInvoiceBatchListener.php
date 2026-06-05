<?php declare(strict_types=1);

namespace App\Listeners\Billing;

use App\Jobs\Billing\CreateRegularPeriodInvoicesJob;
use Core\Domains\Billing\Events\RegularPeriodInvoiceBatchRequested;

class DispatchRegularPeriodInvoiceBatchListener
{
    public function handle(RegularPeriodInvoiceBatchRequested $event): void
    {
        dispatch(new CreateRegularPeriodInvoicesJob($event->periodId, $event->accountIds));
    }
}
