<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use Core\App\Billing\Invoice\CreateRegularPeriodInvoicesInput;
use Core\App\Billing\Invoice\CreateRegularPeriodInvoicesCommand;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * По команде создаёт регулярные счета для всех участков, у которых их ещё нет
 */
class CreateRegularPeriodInvoicesJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int   $periodId,
        private readonly array $accountIds = [],
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::CREATE_REGULAR_PERIOD_INVOICES_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->periodId;
    }

    protected function process(CreateRegularPeriodInvoicesCommand $command): void
    {
        $command->execute(new CreateRegularPeriodInvoicesInput($this->periodId, $this->accountIds));
    }
}
