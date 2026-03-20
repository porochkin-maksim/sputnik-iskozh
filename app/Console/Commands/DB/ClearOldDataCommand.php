<?php declare(strict_types=1);

namespace App\Console\Commands\Back\DB;

use App\Models\Infra\HistoryChanges;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ClearOldDataCommand extends Command
{
    protected $signature   = 'db:clear-old-data';
    protected $description = 'Чистит таблицы от устаревших записей';

    public function handle(): int
    {
        $this->clearHistoryChanges();

        return self::SUCCESS;
    }

    private function clearHistoryChanges(): void
    {
        $cutoffDate = Carbon::now()->subYears(1);

        $this->info(sprintf(
            "Удаляем записи %s старше: %s",
            HistoryChanges::TABLE,
            $cutoffDate->toDateTimeString()
        ));

        $totalDeleted = 0;

        do {
            $deletedInLoop = 0;

            HistoryChanges::where('created_at', '<', $cutoffDate)
                ->orderBy('id')
                ->chunkById(1000, function ($records) use (&$totalDeleted, &$deletedInLoop) {
                    $ids     = $records->pluck('id')->toArray();
                    $deleted = HistoryChanges::whereIn('id', $ids)->delete();

                    $deletedInLoop += $deleted;
                    $totalDeleted  += $deleted;

                    $this->info("Удалено записей в чанке: {$deleted} (всего: {$totalDeleted})");
                });

        } while ($deletedInLoop > 0);

        $this->info("Общее количество удаленных записей: {$totalDeleted}");
    }

}
