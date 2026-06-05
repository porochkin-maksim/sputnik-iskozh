<?php declare(strict_types=1);

namespace App\Jobs\CounterHistory;

use App\Services\Queue\QueueEnum;
use Core\App\CounterHistory\DeleteCounterHistoryFileCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteCounterHistoryFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $counterHistoryId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(DeleteCounterHistoryFileCommand $command): void
    {
        $command->execute($this->counterHistoryId);
    }
}
