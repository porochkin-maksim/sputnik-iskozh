<?php declare(strict_types=1);

namespace App\Jobs\HistoryChanges;

use App\Services\Queue\QueueEnum;
use Core\App\HistoryChanges\CreateHistoryCommand;
use Core\App\HistoryChanges\CreateHistoryInput;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
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

    public function handle(CreateHistoryCommand $command): void
    {
        $command->execute(new CreateHistoryInput($this->historyChanges));
    }
}
