<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Core\App\Billing\Invoice\CreateClaimsAndPaymentsForRegularInvoiceCommand;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Создаёт услуги и платежи для регулярного счёта
 */
class CreateClaimsAndPaymentsForRegularInvoiceJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $invoiceId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::CREATE_CLAIMS_AND_PAYMENTS_FOR_REGULAR_INVOICE_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->invoiceId;
    }

    protected function process(CreateClaimsAndPaymentsForRegularInvoiceCommand $command): void
    {
        $command->execute($this->invoiceId);
    }
}
