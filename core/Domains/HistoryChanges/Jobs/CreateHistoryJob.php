<?php declare(strict_types=1);

namespace Core\Domains\HistoryChanges\Jobs;

use App\Services\Queue\QueueEnum;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private HistoryChangesEntity $historyChanges,
    )
    {
        $this->onQueue(QueueEnum::VERY_LOW->value);
    }

    public function handle(HistoryChangesService $historyChangesService): void
    {
        $historyChangesService->save($this->historyChanges);
    }
}
