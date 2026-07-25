<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use Core\App\Billing\Period\ClosePeriodCommand;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClosePeriodJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $backoff = 10;

    public function __construct(
        private readonly int $periodId,
        private readonly int $userId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::CLOSE_PERIOD_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->periodId;
    }

    protected function process(ClosePeriodCommand $command): void
    {
        $command->execute($this->periodId, $this->userId);
    }
}
