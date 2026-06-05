<?php declare(strict_types=1);

namespace App\Jobs\CounterHistory;

use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Core\App\CounterHistory\NotifyAboutNewUnverifiedCounterHistoryCommand;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyAboutNewUnverifiedCounterHistoryJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $counterHistoryId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::NOTIFY_ABOUT_NEW_UNVERIFIED_COUNTER_HISTORY_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->counterHistoryId;
    }

    public function process(NotifyAboutNewUnverifiedCounterHistoryCommand $command): void
    {
        $command->execute($this->counterHistoryId);
    }
}
