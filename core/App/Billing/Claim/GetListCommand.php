<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

use App\Models\Billing\Claim;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Period\PeriodService;
use Core\Repositories\SearcherInterface;

readonly class GetListCommand
{
    public function __construct(
        private ClaimService $claimService,
        private InvoiceService $invoiceService,
        private PeriodService $periodService,
    )
    {
    }

    public function execute(int $invoiceId): ?ClaimCollection
    {
        $invoice = $this->fetchInvoice($invoiceId);
        if ($invoice === null) {
            return null;
        }

        $claims = $this->claimService->search(
            (new ClaimSearcher())
                ->setInvoiceId($invoiceId)
                ->setWithService()
                ->setSortOrderProperty(Claim::ID, SearcherInterface::SORT_ORDER_ASC)
                ->setSortOrderProperty(Claim::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC),
        )->getItems()->map(function (ClaimEntity $claim) use ($invoice) {
            return $claim->setInvoice($invoice);
        });

        return $claims->sortByServiceTypes();
    }

    private function fetchInvoice(int $invoiceId): ?InvoiceEntity
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        $period = $invoice ? $this->periodService->getById($invoice->getPeriodId()) : null;
        $invoice?->setPeriod($period);

        return $invoice;
    }
}
