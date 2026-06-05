<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Illuminate\Console\Command;

class RecalcPeriodInvoices extends Command
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    )
    {
        parent::__construct();
    }

    protected $signature   = 'billing:invoices:recalc 
                                {--period= : ID периода для пересчёта всех счетов}
                                {--invoice= : Список ID счетов через запятую}';
    protected $description = 'Запускает пересчёт счетов (отправляет джобы)';

    public function handle(): void
    {
        $periodId   = (int) $this->option('period');
        $invoiceRaw = $this->option('invoice');

        if ( ! $periodId && empty($invoiceRaw)) {
            $this->error('Необходимо указать --period или --invoice');

            return;
        }

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

        $searcher = new InvoiceSearcher();
        if ($periodId) {
            $searcher->setPeriodId($periodId);
            $this->info("Поиск счетов за период {$periodId}...");
        }
        else {
            $searcher->setIds($invoiceIds);
            $this->info("Поиск счетов по ID: " . implode(',', $invoiceIds));
        }

        $invoices = $this->invoiceService->search($searcher)->getItems();

        if ($invoices->isEmpty()) {
            $this->warn("Счета не найдены.");

            return;
        }

        $this->info("Найдено счетов: " . $invoices->count());

        $sent    = 0;
        $blocked = 0;
        $errors  = 0;

        $progressBar = $this->output->createProgressBar($invoices->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Обработка...');
        $progressBar->start();

        foreach ($invoices as $invoice) {
            try {
                $result = $this->invoiceService->recalcInvoice($invoice->getId(), true);

                if ($result === true) {
                    $sent++;
                    $progressBar->setMessage("✅ Счёт #{$invoice->getId()} отправлен");
                }
                elseif ($result === false) {
                    $blocked++;
                    $progressBar->setMessage("⏳ Счёт #{$invoice->getId()} уже в очереди");
                }
                else {
                    $errors++;
                    $progressBar->setMessage("❌ Ошибка счёта #{$invoice->getId()}");
                }
            }
            catch (\Throwable $e) {
                $errors++;
                $progressBar->setMessage("❌ Исключение счёта #{$invoice->getId()}: " . $e->getMessage());
            }

            $progressBar->advance();
            usleep(50000); // небольшая задержка для читаемости сообщений (50 мс)
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
