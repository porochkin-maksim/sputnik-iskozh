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

/**
 * По команде создаёт регулярные счета для всех участков, у которых их ещё нет
 */
class CreateRegularPeriodInvoicesJob implements ShouldQueue
{
    private const int LIMIT = 50;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int   $periodId,
        private readonly array $acountIds = [],
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        if ( ! PeriodLocator::PeriodService()->getById($this->periodId)) {
            throw new RuntimeException("Период не найден #{$this->periodId}");
        }

        if ( ! $this->acountIds) {
            $accountIds = InvoiceLocator::InvoiceService()->getAccountsWithoutRegularInvoice($this->periodId)->getIds();
            if ( ! $accountIds) {
                return;
            }
            foreach (array_chunk($accountIds, self::LIMIT) as $accountIdsChunk) {
                dispatch(new self($this->periodId, $accountIdsChunk));
            }

            return;
        }

        foreach ($this->acountIds as $acountId) {
            $invoice = InvoiceLocator::InvoiceFactory()->makeDefault()
                ->setType(InvoiceTypeEnum::REGULAR)
                ->setPeriodId($this->periodId)
                ->setAccountId($acountId)
            ;

            InvoiceLocator::InvoiceService()->save($invoice);
        }
    }
}