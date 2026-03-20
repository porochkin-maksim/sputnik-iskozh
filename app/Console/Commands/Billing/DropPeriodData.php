<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use App\Models\Billing\Claim;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Billing\Period;
use App\Models\File\File;
use App\Models\Infra\HistoryChanges;
use Core\Domains\File\Enums\FileTypeEnum;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DropPeriodData extends Command
{
    protected $signature   = 'billing:period:truncate {periodId : ID периода для очистки} {--force : Выполнить реальное удаление}';
    protected $description = 'Очищает все сущности в период - платежи, счета, историю';

    public function handle(): void
    {
        $periodId = (int) $this->argument('periodId');
        $force    = $this->option('force');
        $dryRun   = ! $force;

        if ($dryRun) {
            $this->warn('Режим DRY-RUN: изменения не будут сохранены в базу данных. Для реального удаления используйте --force');
        }

        $period = Period::find($periodId);
        if ( ! $period) {
            $this->error("Период с ID {$periodId} не найден");

            return;
        }

        $this->info("Период: {$period->name} (ID: {$period->id})");

        if ( ! $this->confirm("Вы уверены, что хотите удалить все данные за этот период? Это действие необратимо!")) {
            $this->info('Операция отменена');

            return;
        }

        $this->info('Начинаю сбор данных...');

        // Получаем ID счетов
        $invoiceIds   = Invoice::where('period_id', $periodId)->pluck('id')->toArray();
        $invoiceCount = count($invoiceIds);
        $this->line("Найдено счетов: {$invoiceCount}");

        $paymentIds   = [];
        $fileIds      = [];
        $paymentCount = 0;
        $filesCount   = 0;

        if ($invoiceCount > 0) {
            // Получаем ID платежей, связанных с этими счетами
            $paymentIds   = Payment::whereIn('invoice_id', $invoiceIds)->pluck('id')->toArray();
            $paymentCount = count($paymentIds);
            $this->line("Найдено платежей: {$paymentCount}");

            // Получаем ID файлов, привязанных к платежам
            if ($paymentCount > 0) {
                $fileIds    = File::whereIn('parent_id', $paymentIds)
                    ->where('type', FileTypeEnum::PAYMENT->value)
                    ->pluck('id')
                    ->toArray()
                ;
                $filesCount = count($fileIds);
                $this->line("Найдено файлов к платежам: {$filesCount}");
            }
        }

        if ($invoiceCount === 0 && $paymentCount === 0) {
            $this->info('Счета и платежи не найдены. Очистка не требуется.');

            return;
        }

        if ($dryRun) {
            $this->info('DRY-RUN: Будет выполнено удаление следующих данных:');
            $this->line(" - Счетов: {$invoiceCount}");
            $this->line(" - Платежей: {$paymentCount}");
            $this->line(" - Файлов платежей: {$filesCount}");
            $this->line(" - Записей истории счетов: {$invoiceCount}");
            $this->line(" - Записей истории платежей: {$paymentCount}");
            $this->info('DRY-RUN завершён. Никакие данные не были изменены.');

            return;
        }

        // Режим реального удаления
        DB::beginTransaction();
        try {
            // Удаляем файлы
            if ($filesCount > 0) {
                $deletedFiles = File::whereIn('id', $fileIds)->forceDelete();
                $this->line("Удалено файлов платежей: {$deletedFiles}");
            }

            // Удаляем платежи
            if ($paymentCount > 0) {
                $deletedPayments = Payment::whereIn('id', $paymentIds)->forceDelete();
                $this->line("Удалено платежей: {$deletedPayments}");
            }

            // Удаляем счета
            $deletedInvoices = Invoice::whereIn('id', $invoiceIds)->forceDelete();
            $this->line("Удалено счетов: {$deletedInvoices}");

            // Удаляем историю по счетам
            $deletedInvoiceHistory = HistoryChanges::where(HistoryChanges::TYPE, HistoryType::INVOICE->value)
                ->whereIn(HistoryChanges::PRIMARY_ID, $invoiceIds)
                ->forceDelete()
            ;
            $this->line("Удалено записей истории счетов: {$deletedInvoiceHistory}");

            // Удаляем историю по платежам
            if ($paymentCount > 0) {
                $deletedPaymentHistory = HistoryChanges::where(HistoryChanges::REFERENCE_TYPE, HistoryType::PAYMENT->value)
                    ->whereIn(HistoryChanges::REFERENCE_ID, $paymentIds)
                    ->forceDelete()
                ;
                $this->line("Удалено записей истории платежей: {$deletedPaymentHistory}");
            }

            DB::commit();
            $this->info('Очистка базы данных успешно завершена.');

            // Запускаем очистку неиспользуемых файлов
            $this->info('Запускаю очистку неиспользуемых файлов...');
            Artisan::call('storage:clear-unused-files');
            $this->line(Artisan::output());
        }
        catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Ошибка при удалении данных: ' . $e->getMessage());
            throw $e;
        }
    }
}