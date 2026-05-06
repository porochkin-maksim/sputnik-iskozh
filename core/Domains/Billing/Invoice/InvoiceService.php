<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice;

use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountService;
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

    public function recalcInvoice(int $invoiceId, bool $sync = false): bool
    {
        return $sync
            ? RecalcClaimsPaidJob::dispatchSyncIfNeeded($invoiceId)
            : RecalcClaimsPaidJob::dispatchIfNeeded($invoiceId);
    }
}
