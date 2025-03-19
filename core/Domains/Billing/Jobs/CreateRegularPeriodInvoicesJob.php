<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class CreateRegularPeriodInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $periodId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        if ( ! PeriodLocator::PeriodService()->getById($this->periodId)) {
            throw new RuntimeException("Период не найден #{$this->periodId}");
        }

        $accounts = InvoiceLocator::InvoiceService()->getAccountsWithoutRegularInvoice($this->periodId);

        foreach ($accounts as $account) {
            $invoice = InvoiceLocator::InvoiceFactory()->makeDefault()
                ->setType(InvoiceTypeEnum::REGULAR)
                ->setPeriodId($this->periodId)
                ->setAccountId($account->getId())
            ;

            InvoiceLocator::InvoiceService()->save($invoice);
        }
    }
}