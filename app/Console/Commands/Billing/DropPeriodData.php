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
use Illuminate\Support\Facades\DB;

class DropPeriodData extends Command
{
    protected $signature   = 'billing:period:truncate {periodId : ID периода для очистки} {--force : Выполнить реальное удаление}';
    protected $description = 'Очищает все сущности в период - услуги, платежи, счета, историю';

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
        $this->info('Начинаю сбор данных...');

        // Получаем ID счетов
        $invoiceIds   = Invoice::where('period_id', $periodId)->pluck('id')->toArray();
        $invoiceCount = count($invoiceIds);

        $paymentIds   = [];
        $claimIds     = [];
        $fileIds      = [];
        $paymentCount = 0;
        $claimCount   = 0;
        $filesCount   = 0;

        if ($invoiceCount > 0) {
            // Получаем ID услуг, связанных с этими счетами
            $claimIds   = Claim::whereIn('invoice_id', $invoiceIds)->pluck('id')->toArray();
            $claimCount = count($claimIds);

            // Получаем ID платежей, связанных с этими счетами
            $paymentIds   = Payment::whereIn('invoice_id', $invoiceIds)->pluck('id')->toArray();
            $paymentCount = count($paymentIds);

            // Получаем ID файлов, привязанных к платежам
            if ($paymentCount > 0) {
                $fileIds    = File::whereIn('parent_id', $paymentIds)
                    ->where('type', FileTypeEnum::PAYMENT->value)
                    ->pluck('id')
                    ->toArray()
                ;
                $filesCount = count($fileIds);
            }
        }

        if ($invoiceCount === 0 && $paymentCount === 0 && $claimCount === 0) {
            $this->info('Счета, услуги и платежи не найдены. Очистка не требуется.');

            return;
        }

        $this->info('Будет выполнено удаление следующих данных:');
        $this->line(" - Счетов: {$invoiceCount}");
        $this->line(" - Услуг: {$claimCount}");
        $this->line(" - Платежей: {$paymentCount}");
        $this->line(" - Файлов платежей: {$filesCount}");
        $this->line(" - Записей истории счетов: {$invoiceCount}");
        $this->line(" - Записей истории услуг: {$claimCount}");
        $this->line(" - Записей истории платежей: {$paymentCount}");

        if ($dryRun) {
            $this->info('DRY-RUN завершён. Никакие данные не были изменены.');

            return;
        }

        if ( ! $this->confirm("Вы уверены, что хотите удалить все данные за этот период? Это действие необратимо!")) {
            $this->info('Операция отменена');

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

            // Удаляем услуги
            if ($claimCount > 0) {
                $deletedClaims = Claim::whereIn('id', $claimIds)->forceDelete();
                $this->line("Удалено услуг: {$deletedClaims}");
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

            // Удаляем историю по услугам
            if ($claimCount > 0) {
                $deletedClaimHistory = HistoryChanges::where(HistoryChanges::TYPE, HistoryType::CLAIM->value)
                    ->whereIn(HistoryChanges::PRIMARY_ID, $claimIds)
                    ->forceDelete()
                ;
                $this->line("Удалено записей истории услуг: {$deletedClaimHistory}");
            }

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
        }
        catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Ошибка при удалении данных: ' . $e->getMessage());
            throw $e;
        }
    }
}