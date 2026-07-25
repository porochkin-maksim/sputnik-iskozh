<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice;

use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountService;
use Core\Repositories\SearcherInterface;
use App\Jobs\Billing\RecalcClaimsPaidJob;

readonly class InvoiceService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private AccountService             $accountService,
    )
    {
    }

    public function search(InvoiceSearcher $searcher): InvoiceSearchResponse
    {
        return $this->invoiceRepository->search($searcher);
    }

    public function getById(?int $id): ?InvoiceEntity
    {
        return $this->invoiceRepository->getById($id);
    }

    public function save(InvoiceEntity $invoice): InvoiceEntity
    {
        return $this->invoiceRepository->save($invoice);
    }

    public function deleteById(?int $id): bool
    {
        return $this->invoiceRepository->deleteById($id);
    }

    public function getAccountsWithoutRegularInvoice(int $periodId): AccountCollection
    {
        return $this->accountService->getByIds($this->invoiceRepository->getAccountIdsWithoutRegularInvoice($periodId));
    }

    public function recalcInvoice(int $invoiceId, bool $sync = false, bool $redistribute = true): bool
    {
        return $sync
            ? RecalcClaimsPaidJob::dispatchSyncIfNeeded($invoiceId, $redistribute)
            : RecalcClaimsPaidJob::dispatchIfNeeded($invoiceId, $redistribute);
    }

    public function getByAccountId(int $accountId): InvoiceCollection
    {
        return $this->search(new InvoiceSearcher()
            ->setAccountId($accountId),
        )->getItems();
    }

    public function getChargesByAccountIdAndPeriodId(int $accountId, array $periodIds = []): InvoiceCollection
    {
        $searcher = new InvoiceSearcher()
            ->setAccountId($accountId)
            ->setWithPeriod()
            ->setWithClaims()
            ->setSortOrderProperty('period_id', SearcherInterface::SORT_ORDER_DESC)
        ;

        if ($periodIds) {
            $searcher->setPeriodIds($periodIds);
        }

        return $this->search($searcher)->getItems();
    }

    public function getByPeriodId(int $periodId): InvoiceCollection
    {
        return $this->search(new InvoiceSearcher()
            ->setPeriodId($periodId),
        )->getItems();
    }
}
