<?php declare(strict_types=1);

namespace Core\App\Billing\Period;

use Carbon\Carbon;
use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodService;
use Core\Exceptions\ValidationException;

readonly class ClosePeriodCommand
{
    public function __construct(
        private PeriodService          $periodService,
        private InvoiceService         $invoiceService,
        private RecalcClaimsPaidCommand $recalcClaimsPaidCommand,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(int $periodId, int $userId): void
    {
        $period = $this->periodService->getById($periodId);

        if ($period === null) {
            throw new ValidationException([], 'Период не найден');
        }

        if ($period->isClosed()) {
            throw new ValidationException([], 'Период уже закрыт');
        }

        $period
            ->setIsClosed(true)
            ->setClosedAt(Carbon::now())
            ->setClosedBy($userId);

        $this->periodService->save($period);

        $invoices = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->setPeriodId($periodId)
                ->setType(InvoiceTypeEnum::REGULAR),
        )->getItems();

        $processed = [];
        foreach ($invoices as $invoice) {
            $accountId = $invoice->getAccountId();
            if ( ! isset($processed[$accountId])) {
                $processed[$accountId] = true;
                $this->recalcClaimsPaidCommand->executeForAccount($accountId);
            }
        }
    }
}
