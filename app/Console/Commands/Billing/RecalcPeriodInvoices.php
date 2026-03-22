<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Jobs\RecalcClaimsPayedJob;
use Illuminate\Console\Command;

class RecalcPeriodInvoices extends Command
{
    protected $signature   = 'billing:invoices:recalc {--period= : Период для пересчёта}';
    protected $description = 'Запускает пересчёт счетов в периоде';

    public function handle(): void
    {
        $periodId = (int) $this->option('period');

        if ( ! $periodId) {
            $this->error("Период не указан или указан неверно");

            return;
        }

        $invoices = InvoiceLocator::InvoiceService()->search(
            InvoiceSearcher::make()
                ->setPeriodId($periodId),
        )->getItems();

        if ($invoices->isEmpty()) {
            $this->info("Счета для периода {$periodId} не найдены");

            return;
        }

        $count = 0;
        foreach ($invoices as $invoice) {
            $this->info("Пересчёт счёта #{$invoice->getId()}");
            dispatch(new RecalcClaimsPayedJob($invoice->getId()));
            $count++;
        }

        $this->info("Отправлено {$count} заданий на пересчёт");
    }
}