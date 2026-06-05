<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use App\Models\Billing\Claim;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Billing\Period;
use App\Models\Files\FileModel;
use App\Models\Infra\HistoryChanges;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PeriodDataTruncate extends Command
{
    protected $signature   = 'billing:period:truncate 
                                {--period= : ID периода для очистки} 
                                {--force : Выполнить реальное удаление} 
                                {--force-snt : Вместе со счетами СНТ} 
                                {--only= : Какие сущности удалить (invoices,claims,payments через запятую)}';
    protected $description = 'Очищает сущности за период (счета, услуги, платежи) с учётом зависимостей';

    private const string ENTITY_INVOICES = 'invoices';
    private const string ENTITY_CLAIMS   = 'claims';
    private const string ENTITY_PAYMENTS = 'payments';

    private const array ALL_ENTITIES = [
        self::ENTITY_INVOICES,
        self::ENTITY_CLAIMS,
        self::ENTITY_PAYMENTS,
    ];

    public function handle(): void
    {
        $periodId = (int) $this->option('period');
        $force    = $this->option('force');
        $withSnt  = $this->option('force-snt');
        $onlyRaw  = $this->option('only');
        $dryRun   = ! $force;

        if ($dryRun) {
            $this->warn('Режим DRY-RUN: изменения не будут сохранены. Для реального удаления используйте --force');
        }

        $period = Period::find($periodId);
        if ( ! $period) {
            $this->error("Период с ID {$periodId} не найден");

            return;
        }

        $this->info("Период: {$period->name} (ID: {$period->id})");

        // Определяем, какие сущности будем удалять
        $entitiesToDelete = $this->resolveEntitiesToDelete($onlyRaw);
        $this->info('Выбрано удаление: ' . implode(', ', $entitiesToDelete));

        // Собираем ID сущностей в зависимости от выбранного набора
        $data = $this->collectData($periodId, $withSnt, $entitiesToDelete);

        // Показываем статистику
        $this->showSummary($data);

        if ($dryRun) {
            $this->info('DRY-RUN завершён. Никакие данные не были изменены.');

            return;
        }

        if ( ! $this->confirm('Вы уверены, что хотите удалить выбранные данные? Это действие необратимо!')) {
            $this->info('Операция отменена');

            return;
        }

        // Выполняем удаление
        DB::beginTransaction();
        try {
            $this->performDeletion($data);
            DB::commit();
            $this->info('Очистка успешно завершена.');
        }
        catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Ошибка при удалении: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Определяет список сущностей для удаления с учётом зависимостей.
     */
    private function resolveEntitiesToDelete(?string $onlyRaw): array
    {
        if (empty($onlyRaw)) {
            return self::ALL_ENTITIES;
        }

        $requested = array_map('trim', explode(',', $onlyRaw));
        $valid     = array_intersect($requested, self::ALL_ENTITIES);

        if (empty($valid)) {
            $this->error('Некорректные значения для --only. Допустимо: ' . implode(',', self::ALL_ENTITIES));
            exit(1);
        }

        // Если удаляем счета, то автоматически добавляем услуги и платежи (чтобы не осталось сирот)
        if (in_array(self::ENTITY_INVOICES, $valid, true)) {
            $valid = array_unique(array_merge($valid, [self::ENTITY_CLAIMS, self::ENTITY_PAYMENTS]));
        }

        return $valid;
    }

    /**
     * Собирает ID сущностей для удаления.
     */
    private function collectData(int $periodId, bool $withSnt, array $entitiesToDelete): array
    {
        $tables = [HistoryChanges::TABLE];
        $data   = [
            'invoices' => [],
            'claims'   => [],
            'payments' => [],
            'files'    => [],
        ];

        // Базовый запрос для счетов (необходим для связей)
        $invoiceQuery = Invoice::where('period_id', $periodId);
        if ( ! $withSnt) {
            $invoiceQuery->where('account_id', '!=', AccountIdEnum::SNT->value);
        }

        // Получаем ID счетов, если они будут удаляться или нужны для связей
        $invoiceIds = [];
        if (in_array(self::ENTITY_INVOICES, $entitiesToDelete, true) ||
            in_array(self::ENTITY_CLAIMS, $entitiesToDelete, true) ||
            in_array(self::ENTITY_PAYMENTS, $entitiesToDelete, true)) {
            $invoiceIds = $invoiceQuery->pluck('id')->toArray();
            if (in_array(self::ENTITY_INVOICES, $entitiesToDelete, true)) {
                $tables[]         = Invoice::TABLE;
                $data['invoices'] = $invoiceIds;
            }
        }

        // Если удаляем услуги (или счета, которые влекут услуги)
        if (in_array(self::ENTITY_CLAIMS, $entitiesToDelete, true)) {
            $tables[]   = Claim::TABLE;
            $claimQuery = Claim::query();
            if ( ! empty($invoiceIds)) {
                // Услуги, привязанные к выбранным счетам
                $claimQuery->whereIn('invoice_id', $invoiceIds);
            }
            else {
                // Если счетов нет, но услуги всё равно могут существовать (хотя по логике не должны)
                $claimQuery->whereHas('invoice', function ($q) use ($periodId, $withSnt) {
                    $q->where('period_id', $periodId);
                    if ( ! $withSnt) {
                        $q->where('account_id', '!=', AccountIdEnum::SNT->value);
                    }
                });
            }
            $data['claims'] = $claimQuery->pluck('id')->toArray();
        }

        // Если удаляем платежи (или счета, которые влекут платежи)
        if (in_array(self::ENTITY_PAYMENTS, $entitiesToDelete, true)) {
            $tables[]     = Payment::TABLE;
            $paymentQuery = Payment::query();
            if ( ! empty($invoiceIds)) {
                $paymentQuery->whereIn('invoice_id', $invoiceIds);
            }
            else {
                $paymentQuery->whereHas('invoice', function ($q) use ($periodId, $withSnt) {
                    $q->where('period_id', $periodId);
                    if ( ! $withSnt) {
                        $q->where('account_id', '!=', AccountIdEnum::SNT->value);
                    }
                });
            }
            $data['payments'] = $paymentQuery->pluck('id')->toArray();
        }

        // Файлы, привязанные к платежам (если удаляем платежи)
        if ( ! empty($data['payments'])) {
            $tables[]      = FileModel::TABLE;
            $data['files'] = FileModel::whereIn('parent_id', $data['payments'])
                ->where('type', FileTypeEnum::PAYMENT->value)
                ->pluck('id')
                ->toArray()
            ;
        }

        $this->resetAutoIncrementIfNeeded($tables);

        return $data;
    }

    /**
     * Выводит статистику по удаляемым данным.
     */
    private function showSummary(array $data): void
    {
        $this->info('Будет выполнено удаление следующих данных:');
        $this->line(" - Счетов: " . count($data['invoices']));
        $this->line(" - Услуг: " . count($data['claims']));
        $this->line(" - Платежей: " . count($data['payments']));
        $this->line(" - Файлов платежей: " . count($data['files']));
        $this->line(" - Записей истории счетов: " . count($data['invoices']));
        $this->line(" - Записей истории услуг: " . count($data['claims']));
        $this->line(" - Записей истории платежей: " . count($data['payments']));
    }

    /**
     * Выполняет реальное удаление.
     */
    private function performDeletion(array $data): void
    {
        // Удаляем файлы
        if ( ! empty($data['files'])) {
            $deleted = FileModel::whereIn('id', $data['files'])->forceDelete();
            $this->line("Удалено файлов платежей: {$deleted}");
        }

        // Удаляем платежи
        if ( ! empty($data['payments'])) {
            $deleted = Payment::whereIn('id', $data['payments'])->forceDelete();
            $this->line("Удалено платежей: {$deleted}");
        }

        // Удаляем услуги
        if ( ! empty($data['claims'])) {
            $deleted = Claim::whereIn('id', $data['claims'])->forceDelete();
            $this->line("Удалено услуг: {$deleted}");
        }

        // Удаляем счета
        if ( ! empty($data['invoices'])) {
            $deleted = Invoice::whereIn('id', $data['invoices'])->forceDelete();
            $this->line("Удалено счетов: {$deleted}");
        }

        // Удаляем историю по счетам
        if ( ! empty($data['invoices'])) {
            $deleted = HistoryChanges::where(HistoryChanges::TYPE, HistoryType::INVOICE->value)
                ->whereIn(HistoryChanges::PRIMARY_ID, $data['invoices'])
                ->forceDelete()
            ;
            $this->line("Удалено записей истории счетов: {$deleted}");
        }

        // Удаляем историю по услугам
        if ( ! empty($data['claims'])) {
            $deleted = HistoryChanges::where(HistoryChanges::TYPE, HistoryType::CLAIM->value)
                ->whereIn(HistoryChanges::PRIMARY_ID, $data['claims'])
                ->forceDelete()
            ;
            $this->line("Удалено записей истории услуг: {$deleted}");
        }

        // Удаляем историю по платежам
        if ( ! empty($data['payments'])) {
            $deleted = HistoryChanges::where(HistoryChanges::REFERENCE_TYPE, HistoryType::PAYMENT->value)
                ->whereIn(HistoryChanges::REFERENCE_ID, $data['payments'])
                ->forceDelete()
            ;
            $this->line("Удалено записей истории платежей: {$deleted}");
        }
    }

    private function resetAutoIncrementIfNeeded(array $tables): void
    {
        foreach (array_unique($tables) as $tableName) {
            if ( ! $tableName) {
                continue;
            }

            $maxId = DB::table($tableName)->max('id') ?? 0;

            $nextId = $maxId + 1;
            DB::statement("ALTER TABLE {$tableName} AUTO_INCREMENT = {$nextId}");
            $this->line("Сброшен AUTO_INCREMENT для таблицы {$tableName} на значение {$nextId}");
        }
    }
}
