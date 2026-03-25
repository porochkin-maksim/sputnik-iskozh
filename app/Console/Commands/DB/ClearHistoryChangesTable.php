<?php declare(strict_types=1);

namespace App\Console\Commands\DB;

use App\Models\Access\Role;
use App\Models\Account\Account;
use App\Models\Billing\Claim;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Billing\Period;
use App\Models\Billing\Service;
use App\Models\Counter\Counter;
use App\Models\Counter\CounterHistory;
use App\Models\Infra\HistoryChanges;
use App\Models\Infra\Option;
use App\Models\User;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearHistoryChangesTable extends Command
{
    protected $signature = 'db:clear-history-changes
                            {--force : Выполнить реальное удаление}';

    protected $description = 'Очищает таблицу логов (history_changes) от записей, ссылающихся на несуществующие сущности. Без --force выводит только статистику.';

    /**
     * Сопоставление типов (использующих primary_id) с таблицами.
     */
    private const array PRIMARY_TYPES_TO_TABLE = [
        HistoryType::SERVICE->value         => Service::TABLE,
        HistoryType::PERIOD->value          => Period::TABLE,
        HistoryType::ACCOUNT->value         => Account::TABLE,
        HistoryType::INVOICE->value         => Invoice::TABLE,
        HistoryType::USER->value            => User::TABLE,
        HistoryType::ROLE->value            => Role::TABLE,
        HistoryType::COUNTER->value         => Counter::TABLE,
        HistoryType::COUNTER_HISTORY->value => CounterHistory::TABLE,
        HistoryType::OPTION->value          => Option::TABLE,
    ];

    /**
     * Сопоставление типов (использующих reference_type и reference_id) с таблицами.
     */
    private const array REFERENCE_TYPES_TO_TABLE = [
        HistoryType::CLAIM->value   => Claim::TABLE,
        HistoryType::PAYMENT->value => Payment::TABLE,
    ];

    public function handle(): int
    {
        $force = $this->option('force');

        // Собираем статистику по сиротским записям
        $stats = $this->collectStats();

        $this->showStats($stats);

        $totalToDelete = array_sum($stats['primary']) + array_sum($stats['reference']);

        if ($totalToDelete === 0) {
            $this->info('Сиротские записи не найдены. Очистка не требуется.');

            return 0;
        }

        if ( ! $force) {
            $this->warn('Для реального удаления выполните команду с --force');

            return 0;
        }

        if ( ! $this->confirm('Вы уверены, что хотите удалить найденные сиротские записи? Это действие необратимо!')) {
            $this->info('Операция отменена.');

            return 0;
        }

        DB::beginTransaction();
        try {
            $deleted = 0;
            $deleted += $this->cleanOrphanPrimaryRecords($stats['primary_ids']);
            $deleted += $this->cleanOrphanReferenceRecords($stats['reference_ids']);

            DB::commit();
            $this->info("Удалено записей: {$deleted}");
        }
        catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Ошибка при удалении: ' . $e->getMessage());
            throw $e;
        }

        return 0;
    }

    /**
     * Собирает статистику по сиротским записям.
     */
    private function collectStats(): array
    {
        $stats = [
            'primary'       => [],
            'primary_ids'   => [],
            'reference'     => [],
            'reference_ids' => [],
        ];

        // primary_id записи (type заполнен, reference_type отсутствует)
        foreach (self::PRIMARY_TYPES_TO_TABLE as $type => $table) {
            $tableName = $this->getTableName($table);
            if ( ! $tableName) {
                $this->warn("Таблица для типа '" . HistoryType::names()[$type] . "' не найдена. Пропускаем.");
                continue;
            }

            $orphanIds = HistoryChanges::where('type', $type)
                ->whereNull('reference_type') // добавляем условие, чтобы не смешивать с reference-записями
                ->whereNotExists(function ($query) use ($tableName) {
                    $query->select(DB::raw(1))
                        ->from($tableName)
                        ->whereRaw(DB::raw("{$tableName}.id = history_changes.primary_id"))
                    ;
                })
                ->pluck('id')
                ->toArray()
            ;

            $stats['primary'][$type]     = count($orphanIds);
            $stats['primary_ids'][$type] = $orphanIds;
        }

        // reference_id записи (reference_type заполнен, type отсутствует)
        foreach (self::REFERENCE_TYPES_TO_TABLE as $type => $table) {
            $tableName = $this->getTableName($table);
            if ( ! $tableName) {
                $this->warn("Таблица для типа '" . HistoryType::names()[$type] . "' не найдена. Пропускаем.");
                continue;
            }

            $orphanIds = HistoryChanges::where('reference_type', $type)
                ->whereNull('type') // добавляем условие, чтобы не смешивать с primary-записями
                ->whereNotExists(function ($query) use ($tableName) {
                    $query->select(DB::raw(1))
                        ->from($tableName)
                        ->whereRaw(DB::raw("{$tableName}.id = history_changes.reference_id"))
                    ;
                })
                ->pluck('id')
                ->toArray()
            ;

            $stats['reference'][$type]     = count($orphanIds);
            $stats['reference_ids'][$type] = $orphanIds;
        }

        return $stats;
    }

    /**
     * Выводит собранную статистику в виде таблицы.
     */
    private function showStats(array $stats): void
    {
        $rows = [];

        foreach ($stats['primary'] as $type => $count) {
            if ($count > 0) {
                $rows[] = [
                    HistoryType::names()[$type] ?? "Тип {$type}",
                    'primary_id',
                    $count,
                ];
            }
        }

        foreach ($stats['reference'] as $type => $count) {
            if ($count > 0) {
                $rows[] = [
                    HistoryType::names()[$type] ?? "Тип {$type}",
                    'reference_id',
                    $count,
                ];
            }
        }

        if (empty($rows)) {
            $this->info('Сиротские записи не найдены.');

            return;
        }

        $this->table(
            ['Тип сущности', 'Поле связи', 'Количество'],
            $rows,
        );

        $total = array_sum($stats['primary']) + array_sum($stats['reference']);
        $this->line("Итого: {$total} записей");
    }

    /**
     * Удаляет записи с primary_id, ссылающиеся на несуществующие записи.
     */
    private function cleanOrphanPrimaryRecords(array $primaryIdsMap): int
    {
        $deleted = 0;
        foreach ($primaryIdsMap as $type => $ids) {
            if (empty($ids)) {
                continue;
            }
            $deleted += HistoryChanges::whereIn('id', $ids)->forceDelete();
        }

        return $deleted;
    }

    /**
     * Удаляет записи с reference_type и reference_id, ссылающиеся на несуществующие записи.
     */
    private function cleanOrphanReferenceRecords(array $referenceIdsMap): int
    {
        $deleted = 0;
        foreach ($referenceIdsMap as $type => $ids) {
            if (empty($ids)) {
                continue;
            }
            $deleted += HistoryChanges::whereIn('id', $ids)->forceDelete();
        }

        return $deleted;
    }

    /**
     * Возвращает реальное имя таблицы по имени модели или по имени таблицы.
     */
    private function getTableName(string $tableOrModel): ?string
    {
        // Если передано имя модели, получаем таблицу через модель
        if (class_exists($tableOrModel) && is_subclass_of($tableOrModel, \Illuminate\Database\Eloquent\Model::class)) {
            return (new $tableOrModel)->getTable();
        }

        // Если передано имя таблицы, проверяем существование
        if (Schema::hasTable($tableOrModel)) {
            return $tableOrModel;
        }

        return null;
    }
}