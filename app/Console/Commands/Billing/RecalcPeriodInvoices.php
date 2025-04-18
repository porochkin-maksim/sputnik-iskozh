<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Jobs\RecalcClaimsPayedJob;
use Illuminate\Console\Command;

class RecalcPeriodInvoices extends Command
{
    protected $signature   = 'billing:invoices:recalc {--periodId= : Period id to recalculate}';
    protected $description = 'Creates services for each period if they doesnt exist';

    public function handle(): void
    {
        $periodId = (int) $this->option('periodId');

        if ( ! $periodId) {
            $this->error("Please specify periodId {$periodId}");

            return;
        }

        $invoices = InvoiceLocator::InvoiceService()->search(
            InvoiceSearcher::make()
                ->setPeriodId((int) $periodId),
        )->getItems();

        foreach ($invoices as $invoice) {
            $this->info("Recalculating claims for invoice #{$invoice->getId()}");
            dispatch_sync(new RecalcClaimsPayedJob($invoice->getId()));
        }
    }
} 