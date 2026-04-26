<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
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
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const int LIMIT = 50;

    public function __construct(
        private readonly int   $periodId,
        private readonly array $acountIds = [],
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

    protected function process(
        PeriodService $periodService,
        InvoiceService $invoiceService,
        InvoiceFactory $invoiceFactory,
    ): void
    {
        if ( ! $periodService->getById($this->periodId)) {
            throw new RuntimeException("Период не найден #{$this->periodId}");
        }

        if ( ! $this->acountIds) {
            $accountIds = $invoiceService->getAccountsWithoutRegularInvoice($this->periodId)->getIds();
            if ( ! $accountIds) {
                return;
            }
            foreach (array_chunk($accountIds, self::LIMIT) as $accountIdsChunk) {
                dispatch(new self($this->periodId, $accountIdsChunk));
            }

            return;
        }

        foreach ($this->acountIds as $acountId) {
            $invoice = $invoiceFactory->makeDefault()
                ->setType(InvoiceTypeEnum::REGULAR)
                ->setPeriodId($this->periodId)
                ->setAccountId($acountId)
            ;

            $invoiceService->save($invoice);
        }
    }
}
