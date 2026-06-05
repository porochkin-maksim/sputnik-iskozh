<?php declare(strict_types=1);

namespace App\Jobs\CounterHistory;

use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Core\App\CounterHistory\RewatchCounterHistoryChainCommand;
use Core\App\CounterHistory\RewatchCounterHistoryChainInput;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RewatchCounterHistoryChainJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $counterId,
    )
    {
        $this->onQueue(QueueEnum::LOW->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::REWATCH_COUNTER_HISTORY_CHAIN_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->counterId;
    }

    public function process(RewatchCounterHistoryChainCommand $command): void
    {
        $command->execute(new RewatchCounterHistoryChainInput($this->counterId));
    }
}
