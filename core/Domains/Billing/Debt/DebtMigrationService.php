<?php declare(strict_types=1);

namespace Core\Domains\Billing\Debt;

use App\Models\Billing\Claim;
use App\Models\Billing\Invoice;
use App\Models\Billing\Period;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\Repositories\SearcherInterface;

readonly class DebtMigrationService
{
    public function __construct(
        private PeriodService  $periodService,
        private InvoiceService $invoiceService,
        private ClaimService   $claimService,
    )
    {
    }

    public function getUnmigratedOldDebts(InvoiceEntity $invoice): ClaimCollection
    {
        $result = new ClaimCollection();

        $currentPeriod = $this->periodService->getById($invoice->getPeriodId());
        if ($currentPeriod === null || $currentPeriod->getStartAt() === null) {
            return $result;
        }

        $period = $this->periodService->search(
            PeriodSearcher::make()
                ->setIsClosed(true)
                ->addWhere(Period::END_AT, SearcherInterface::LT, $currentPeriod->getStartAt())
                ->setSortOrderProperty(Period::END_AT, SearcherInterface::SORT_ORDER_DESC)
                ->setLimit(1),
        )->getItems()->first();

        if ($period === null) {
            return $result;
        }

        $previousInvoice = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->setPeriodId($period->getId())
                ->setAccountId($invoice->getAccountId())
                ->setType(InvoiceTypeEnum::REGULAR)
                ->setLimit(1),
        )->getItems()->first();

        if ($previousInvoice === null) {
            return $result;
        }

        $previousInvoice->setPeriod($period);

        $oldDebts = $this->claimService->search(
            ClaimSearcher::make()
                ->setWithService()
                ->setInvoiceId($previousInvoice->getId()),
        )->getItems()
            ->map(static fn(ClaimEntity $claim) => $claim->setInvoice($previousInvoice))
            ->filter(static fn(ClaimEntity $claim) => $claim->getDelta())
        ;

        if ($oldDebts->isEmpty()) {
            return $result;
        }

        $openPeriods = $this->periodService->getOpenPeriods();
        if ($openPeriods->isEmpty()) {
            return $oldDebts;
        }

        $oldDebtIds    = $oldDebts->map(static fn(ClaimEntity $c) => $c->getId())->toArray();
        $openPeriodIds = $openPeriods->map(static fn($p) => $p->getId())->toArray();

        $openInvoices = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->addWhereIn(Invoice::PERIOD_ID, $openPeriodIds)
                ->setAccountId($invoice->getAccountId())
                ->setType(InvoiceTypeEnum::REGULAR),
        )->getItems();

        if ($openInvoices->isNotEmpty()) {
            $openInvoiceIds = $openInvoices->map(static fn(InvoiceEntity $i) => $i->getId())->toArray();

            $alreadyMigrated = $this->claimService->search(
                ClaimSearcher::make()
                    ->addWhereIn(Claim::INVOICE_ID, $openInvoiceIds)
                    ->addWhereIn(Claim::ORIGINAL_CLAIM_ID, $oldDebtIds)
                    ->addWhere(Claim::ORIGINAL_SERVICE_ID, SearcherInterface::IS_NOT_NULL),
            )->getItems()
                ->map(static fn(ClaimEntity $c) => $c->getOriginalClaimId())
                ->toArray()
            ;

            return $oldDebts->filter(
                static fn(ClaimEntity $c) => ! in_array($c->getId(), $alreadyMigrated, true),
            );
        }

        return $oldDebts;
    }
}
