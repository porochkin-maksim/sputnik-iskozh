<?php declare(strict_types=1);

namespace App\Console\Commands\DB;

use App\Models\Infra\Lock;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearLocksTable extends Command
{
    protected $signature = 'db:clear-locks 
                            {--lock-name= : Удалить блокировки с указанным именем} 
                            {--expired-only : Удалить только истекшие блокировки} 
                            {--older-than= : Удалить блокировки старше указанных минут (например: 60)} 
                            {--force : Выполнить без подтверждения} 
                            {--strict : Точное совпадение имени блокировки (иначе LIKE %name%)}
                            {--show-names : Вывести список очередей}
                            {--show-only : Только показать статистику, без удаления}';

    protected $description = 'Очищает таблицу блокировок (Lock)';

    public function handle(): int
    {
        $lockName    = $this->option('lock-name');
        $expiredOnly = $this->option('expired-only');
        $olderThan   = $this->option('older-than');
        $force       = $this->option('force');
        $strict      = $this->option('strict');
        $showOnly    = $this->option('show-only');
        $showNames   = $this->option('show-names');

        // Формируем запрос
        $query = Lock::query();

        // Фильтр по имени
        if ($lockName) {
            if ($strict) {
                // Точное совпадение
                $query->where(Lock::NAME, $lockName);
                $this->line("Фильтр по имени (точное совпадение): {$lockName}");
            }
            else {
                // Поиск по LIKE
                $query->where(Lock::NAME, 'like', "%{$lockName}%");
                $this->line("Фильтр по имени (содержит): {$lockName}");
            }
        }

        // Фильтр по времени
        if ($olderThan) {
            $minutes    = (int) $olderThan;
            $cutoffTime = Carbon::now()->subMinutes($minutes);
            $query->where(Lock::EXPIRE_AT, '<=', $cutoffTime);
            $this->line("Фильтр: блокировки старше {$minutes} минут (созданы до {$cutoffTime->format('Y-m-d H:i:s')})");
        }
        elseif ($expiredOnly) {
            $query->where(Lock::EXPIRE_AT, '<=', Carbon::now());
            $this->line("Фильтр: только истекшие блокировки");
        }

        // Получаем статистику
        $totalCount = $query->count();

        // Статистика по активным/истекшим с учётом фильтров
        $activeQuery  = clone $query;
        $activeCount  = $activeQuery->where(Lock::EXPIRE_AT, '>', Carbon::now())->count();
        $expiredCount = $totalCount - $activeCount;

        // Общая статистика по таблице
        $totalAll   = Lock::count();
        $activeAll  = Lock::where(Lock::EXPIRE_AT, '>', Carbon::now())->count();
        $expiredAll = $totalAll - $activeAll;

        // Выводим статистику
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                 СТАТИСТИКА БЛОКИРОВОК                    ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->table(
            ['Тип', 'Всего', 'Активные', 'Истекшие'],
            [
                ['Все блокировки', $totalAll, $activeAll, $expiredAll],
                ['Будут удалены', $totalCount, $activeCount, $expiredCount],
            ],
        );

        // Группировка по именам
        if ($totalCount > 0 && $showNames) {
            $this->newLine();
            $this->info('=== Распределение по именам (будут удалены) ===');
            $grouped = (clone $query)
                ->select(Lock::NAME, DB::raw('count(*) as total'))
                ->groupBy(Lock::NAME)
                ->orderBy('total', 'desc')
                ->get()
            ;

            if ($grouped->isNotEmpty()) {
                $this->table(
                    ['Имя блокировки', 'Количество'],
                    $grouped->map(fn($item) => [$item->name, $item->total])->toArray(),
                );
            }
        }

        // Показываем примеры блокировок
        if ($totalCount > 0 && $totalCount <= 20) {
            $this->newLine();
            $this->info('=== Список блокировок для удаления ===');
            $samples = (clone $query)
                ->orderBy(Lock::EXPIRE_AT, 'desc')
                ->get()
            ;

            $this->table(
                ['ID', 'Имя', 'Создана', 'Истекает', 'Статус'],
                $samples->map(function ($lock) {
                    $createdAt = Carbon::parse($lock->created_at);
                    $expireAt  = Carbon::parse($lock->expire_at);
                    $status    = $expireAt->isPast() ? '❌ Истекла' : '✅ Активна';
                    $timeLeft  = $expireAt->isPast()
                        ? $expireAt->diffForHumans()
                        : 'истекает ' . $expireAt->diffForHumans();

                    return [
                        $lock->id,
                        $lock->name,
                        $createdAt->format('Y-m-d H:i:s') . " ({$createdAt->diffForHumans()})",
                        $expireAt->format('Y-m-d H:i:s') . " ({$timeLeft})",
                        $status,
                    ];
                })->toArray(),
            );
        }
        elseif ($totalCount > 0) {
            $this->newLine();
            $this->info('=== Примеры блокировок (первые 10) ===');
            $samples = (clone $query)
                ->orderBy(Lock::EXPIRE_AT, 'desc')
                ->limit(10)
                ->get()
            ;

            $this->table(
                ['ID', 'Имя', 'Создана', 'Истекает', 'Статус'],
                $samples->map(function ($lock) {
                    $createdAt = Carbon::parse($lock->created_at);
                    $expireAt  = Carbon::parse($lock->expire_at);
                    $status    = $expireAt->isPast() ? '❌ Истекла' : '✅ Активна';

                    return [
                        $lock->id,
                        $lock->name,
                        $createdAt->format('Y-m-d H:i:s'),
                        $expireAt->format('Y-m-d H:i:s'),
                        $status,
                    ];
                })->toArray(),
            );
            $this->line("... и еще " . ($totalCount - 10) . " записей");
        }

        if ($showOnly) {
            $this->newLine();
            $this->info('Режим просмотра: удаление не выполнено');

            return self::SUCCESS;
        }

        if ($totalCount === 0) {
            $this->newLine();
            $this->info('Нет блокировок для удаления.');

            return self::SUCCESS;
        }

        // Предупреждение об активных блокировках
        if ($activeCount > 0 && ! $expiredOnly && ! $olderThan) {
            $this->newLine();
            $this->error("⚠️  ВНИМАНИЕ: Вы собираетесь удалить {$activeCount} активных блокировок!");
            $this->warn('Удаление активных блокировок может привести к конфликтам в работающих процессах.');

            if ( ! $force && ! $this->confirm('Вы уверены, что хотите удалить активные блокировки?', false)) {
                $this->info('Операция отменена');

                return self::SUCCESS;
            }
        }
        else {
            if ( ! $force && ! $this->confirm("Вы уверены, что хотите удалить {$totalCount} блокировок?")) {
                $this->info('Операция отменена');

                return self::SUCCESS;
            }
        }

        // Выполняем удаление
        try {
            DB::beginTransaction();

            $deletedCount = $query->delete();

            DB::commit();

            $this->newLine();
            $this->info("✅ Успешно удалено блокировок: {$deletedCount}");

            // Показываем оставшиеся блокировки
            $remainingTotal  = Lock::count();
            $remainingActive = Lock::where(Lock::EXPIRE_AT, '>', Carbon::now())->count();

            if ($remainingTotal > 0) {
                $this->info("Осталось блокировок: {$remainingTotal} (активных: {$remainingActive})");
            }
            else {
                $this->info('Таблица блокировок полностью очищена');
            }

            return self::SUCCESS;

        }
        catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Ошибка при удалении блокировок: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());

            return self::FAILURE;
        }
    }
}