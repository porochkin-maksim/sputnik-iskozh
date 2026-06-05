<?php declare(strict_types=1);

namespace App\Jobs\CounterHistory;

use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Core\App\CounterHistory\AutoIncrementingCounterHistoriesCommand;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoIncrementingCounterHistoriesJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue(QueueEnum::LOW->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::AUTO_INCREMENTING_COUNTER_HISTORIES_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return null;
    }

    public function process(AutoIncrementingCounterHistoriesCommand $command): void
    {
        $command->execute();
    }
}
