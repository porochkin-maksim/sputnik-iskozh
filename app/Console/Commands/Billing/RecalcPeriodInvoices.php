<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Illuminate\Console\Command;

class RecalcPeriodInvoices extends Command
{
    protected $signature   = 'billing:invoices:recalc 
                                {--period= : ID периода для пересчёта всех счетов}
                                {--invoice= : Список ID счетов через запятую}';
    protected $description = 'Запускает пересчёт счетов (отправляет джобы)';

    public function handle(): void
    {
        $periodId   = (int) $this->option('period');
        $invoiceRaw = $this->option('invoice');

        // Проверяем, что задан либо период, либо список счетов
        if ( ! $periodId && empty($invoiceRaw)) {
            $this->error('Необходимо указать --period или --invoice');

            return;
        }

        // Формируем список ID счетов
        $invoiceIds = [];
        if ( ! empty($invoiceRaw)) {
            $invoiceIds = array_filter(
                array_map('intval', explode(',', $invoiceRaw)),
                fn($id) => $id > 0,
            );
            if (empty($invoiceIds)) {
                $this->error('--invoice содержит некорректные ID');

                return;
            }
        }

        // Получаем счета
        $searcher = new InvoiceSearcher();
        if ($periodId) {
            $searcher->setPeriodId($periodId);
            $this->info("Поиск счетов за период {$periodId}...");
        }
        else {
            $searcher->setIds($invoiceIds);
            $this->info("Поиск счетов по ID: " . implode(',', $invoiceIds));
        }

        $invoices = InvoiceLocator::InvoiceService()->search($searcher)->getItems();

        if ($invoices->isEmpty()) {
            $this->warn("Счета не найдены.");

            return;
        }

        $this->info("Найдено счетов: " . $invoices->count());

        $sent    = 0;
        $blocked = 0;
        $errors  = 0;

        $progressBar = $this->output->createProgressBar($invoices->count());
        $progressBar->start();

        foreach ($invoices as $invoice) {
            $result = InvoiceLocator::InvoiceService()->recalcInvoice($invoice->getId(), true);

            if ($result === true) {
                $sent++;
                $this->line("\n✅ Пересчёт счёта #{$invoice->getId()} отправлен");
            }
            elseif ($result === false) {
                $blocked++;
                $this->warn("\n⏳ Счёт #{$invoice->getId()} уже в очереди (блокировка)");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("📊 Результат:");
        $this->line("   - Отправлено: {$sent}");
        if ($blocked) {
            $this->warn("   - Заблокировано (уже в работе): {$blocked}");
        }
        if ($errors) {
            $this->error("   - Ошибок: {$errors}");
        }
        $this->info("Готово.");
    }
}