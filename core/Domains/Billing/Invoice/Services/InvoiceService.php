<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Services;

use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Invoice\Repositories\InvoiceRepository;
use Core\Domains\Billing\Invoice\Responses\InvoiceSearchResponse;

readonly class InvoiceService
{
    public function __construct(
        private InvoiceRepository $invoiceRepository,
    )
    {
    }

    public function search(InvoiceSearcher $searcher): InvoiceSearchResponse
    {
        return $this->invoiceRepository->search($searcher);
    }

    public function getById(?int $id): ?InvoiceDTO
    {
        return $this->invoiceRepository->getById($id);
    }

    public function save(InvoiceDTO $invoice): InvoiceDTO
    {
        return $this->invoiceRepository->save($invoice);
    }

    public function deleteById(?int $id): bool
    {
        return $this->invoiceRepository->deleteById($id);
    }

    public function getAccountsWithoutRegularInvoice(int $periodId): AccountCollection
    {
        $result = $this->invoiceRepository->getAccountsWithoutRegularInvoice($periodId)->map(function ($item) {
            return $item->id;
        })->toArray();

        return AccountLocator::AccountService()->getByIds($result);
    }
}
