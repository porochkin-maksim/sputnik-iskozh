<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Пересчитывает оплату claims в счёте по платежам
 */
class RecalcClaimsPaidJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Задержка между попытками (секунды)
     */
    public int $backoff = 10;

    public function __construct(
        private readonly int  $invoiceId,
        private readonly bool $redistribute = true,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::RECALC_CLAIMS_PAID_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->invoiceId;
    }

    protected function process(RecalcClaimsPaidCommand $command): void
    {
        $command->execute($this->invoiceId, $this->redistribute);
    }
}
