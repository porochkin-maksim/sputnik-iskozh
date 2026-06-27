<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Jobs\Billing\CreateClaimsAndPaymentsForRegularInvoiceJob;
use Core\Contracts\EventDispatcherInterface;
use Core\Domains\Billing\Events\RegularPeriodInvoiceBatchRequested;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodService;
use RuntimeException;

readonly class CreateRegularPeriodInvoicesCommand
{
    private const int LIMIT = 50;

    public function __construct(
        private PeriodService            $periodService,
        private InvoiceService           $invoiceService,
        private InvoiceFactory           $invoiceFactory,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function execute(CreateRegularPeriodInvoicesInput $input): void
    {
        if ( ! $this->periodService->getById($input->periodId)) {
            throw new RuntimeException("Период не найден #{$input->periodId}");
        }

        if ( ! $input->accountIds) {
            $this->dispatchBatches($input->periodId);

            return;
        }

        foreach ($input->accountIds as $accountId) {
            $invoice = $this->invoiceFactory->makeDefault()
                ->setType(InvoiceTypeEnum::REGULAR)
                ->setPeriodId($input->periodId)
                ->setAccountId($accountId)
            ;

            $invoice = $this->invoiceService->save($invoice);

            CreateClaimsAndPaymentsForRegularInvoiceJob::dispatchIfNeeded($invoice->getId());
        }
    }

    private function dispatchBatches(int $periodId): void
    {
        $accountIds = $this->invoiceService->getAccountsWithoutRegularInvoice($periodId)->getIds();
        if ( ! $accountIds) {
            return;
        }

        $events = [];
        foreach (array_chunk($accountIds, self::LIMIT) as $accountIdsChunk) {
            $events[] = new RegularPeriodInvoiceBatchRequested($periodId, $accountIdsChunk);
        }

        $this->eventDispatcher->dispatch($events);
    }
}
