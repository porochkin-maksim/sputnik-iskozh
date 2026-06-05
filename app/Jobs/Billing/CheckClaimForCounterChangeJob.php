<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Core\App\Billing\Claim\CheckClaimForCounterChangeCommand;
use Core\App\Billing\Claim\CheckClaimForCounterChangeInput;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckClaimForCounterChangeJob implements ShouldQueue
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
        return LockNameEnum::CHECK_CLAIM_FOR_COUNTER_CHANGE_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->counterHistoryId;
    }

    public function process(CheckClaimForCounterChangeCommand $command): void
    {
        $command->execute(new CheckClaimForCounterChangeInput($this->counterHistoryId));
    }
}
