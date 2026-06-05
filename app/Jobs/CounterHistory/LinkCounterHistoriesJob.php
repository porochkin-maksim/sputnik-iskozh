<?php declare(strict_types=1);

namespace App\Jobs\CounterHistory;

use App\Services\Queue\QueueEnum;
use Core\App\CounterHistory\LinkCounterHistoriesCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LinkCounterHistoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $olderHistoryId,
        private readonly int $newerHistoryId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(LinkCounterHistoriesCommand $command): void
    {
        $command->execute($this->olderHistoryId, $this->newerHistoryId);
    }
}
