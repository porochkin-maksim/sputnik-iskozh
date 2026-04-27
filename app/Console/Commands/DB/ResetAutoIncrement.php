<?php declare(strict_types=1);

namespace App\Console\Commands\DB;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetAutoIncrement extends Command
{
    protected $signature = 'db:reset-auto-increment
                            {--table= : Название таблицы (можно указать несколько через запятую)}
                            {--force : Выполнить изменения (без --force — только просмотр)}';

    protected $description = 'Сбрасывает AUTO_INCREMENT для таблиц MySQL с колонкой id на значение MAX(id)+1';

    public function handle(): int
    {
        $force       = $this->option('force');
        $tablesInput = $this->option('table');

        if ($tablesInput) {
            $tables = array_map('trim', explode(',', $tablesInput));
        }
        else {
            $tables = $this->getAllTables();
        }

        if (empty($tables)) {
            $this->warn('Таблицы не найдены.');

            return 0;
        }

        $rows = [];

        foreach ($tables as $table) {
            $row = ['table' => $table];

            // Проверяем наличие колонки id
            if ( ! $this->hasIdColumn($table)) {
                $row['max_id']     = '-';
                $row['new_value']  = '-';
                $row['current_ai'] = '-';
                $row['status']     = '-'; //'⏭️ пропущено (нет id)';
                $rows[]            = $row;
                continue;
            }

            // Получаем максимальный id
            $maxId  = (int) DB::table($table)->max('id');
            $nextId = $maxId === 0 ? 1 : $maxId + 1;

            // Получаем текущее значение AUTO_INCREMENT
            $currentAi = $this->getCurrentAutoIncrement($table);

            $row['max_id']     = $maxId === 0 ? 'NULL' : $maxId;
            $row['new_value']  = $nextId;
            $row['current_ai'] = $currentAi;

            if ($nextId == $currentAi) {
                $row['status'] = '✅ уже корректен';
            }
            elseif ($currentAi > $nextId) {
                $row['status'] = '🔽 будет уменьшен';
            }
            else {
                $row['status'] = '🔼 будет увеличен';
            }

            if ($force && $nextId != $currentAi) {
                try {
                    DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = {$nextId}");
                    // Принудительно обновляем статистику таблицы, чтобы информация в INFORMATION_SCHEMA обновилась
                    DB::statement("ANALYZE TABLE `{$table}`");
                    $row['status'] = '✅ сброшен';
                }
                catch (\Throwable $e) {
                    $row['status'] = '❌ ошибка: ' . $e->getMessage();
                }
            }
            elseif ($force) {
                // Если уже корректен, просто подтверждаем
                $row['status'] = '✅ уже корректен';
            }

            $rows[] = $row;
        }

        $this->newLine();

        $headers = ['Таблица', 'MAX(id)', 'Текущий AI', 'Новый AI', 'Статус'];
        $this->table($headers, array_map(function ($row) {
            return [
                $row['table'],
                $row['max_id'],
                $row['current_ai'],
                $row['new_value'],
                $row['status'],
            ];
        }, $rows));

        $this->newLine();

        if ( ! $force) {
            $this->info('DRY-RUN: Никакие изменения не были применены. Для выполнения укажите --force');
        }
        else {
            $this->info('Готово.');
        }

        return 0;
    }

    /**
     * Получает текущее значение AUTO_INCREMENT для таблицы.
     */
    private function getCurrentAutoIncrement(string $table): int
    {
        $dbName = DB::getDatabaseName();
        $result = DB::select("SELECT `AUTO_INCREMENT` FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` = ?", [$dbName, $table]);
        if (empty($result) || ! isset($result[0]->AUTO_INCREMENT)) {
            return 1; // если не удалось получить, считаем 1
        }

        return (int) $result[0]->AUTO_INCREMENT;
    }

    private function getAllTables(): array
    {
        $tables = DB::select('SHOW TABLES');
        $tables = array_map('current', $tables);

        $exclude = [
            'migrations', 'failed_jobs', 'password_resets',
            'personal_access_tokens', 'jobs', 'sessions', 'cache', 'cache_locks',
        ];

        return array_values(array_filter($tables, static fn($t) => ! in_array($t, $exclude)));
    }

    private function hasIdColumn(string $table): bool
    {
        return Schema::hasColumn($table, 'id');
    }
}