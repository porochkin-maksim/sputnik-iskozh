<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Services;

use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Account\Jobs\UpdateSntBalanceJob;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Core\Domains\Billing\Invoice\Events\InvoiceCreatedEvent;
use Core\Domains\Billing\Invoice\Factories\InvoiceFactory;
use Core\Domains\Billing\Invoice\Models\InvoiceComparator;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Invoice\Repositories\InvoiceRepository;
use Core\Domains\Billing\Invoice\Responses\SearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

readonly class InvoiceService
{
    public function __construct(
        private InvoiceFactory        $invoiceFactory,
        private InvoiceRepository     $invoiceRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(InvoiceDTO $invoice): InvoiceDTO
    {
        $model = $this->invoiceRepository->getById($invoice->getId());
        if ($model) {
            $before = $this->invoiceFactory->makeDtoFromObject($model);
        }
        else {
            $before = new InvoiceDTO();
        }

        $model   = $this->invoiceRepository->save($this->invoiceFactory->makeModelFromDto($invoice, $model));
        $current = $this->invoiceFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $invoice->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::INVOICE,
            $current->getId(),
            null,
            null,
            new InvoiceComparator($current),
            new InvoiceComparator($before),
        );

        if ( ! $invoice->getId()) {
            InvoiceCreatedEvent::dispatch($current->getId());
            $current = $this->getById($current->getId());
        }

        return $current;
    }

    public function search(InvoiceSearcher $searcher): SearchResponse
    {
        $response = $this->invoiceRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new InvoiceCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->invoiceFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?InvoiceDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new InvoiceSearcher();
        $searcher->setId($id);
        $result = $this->invoiceRepository->search($searcher)->getItems()->first();

        return $result ? $this->invoiceFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        $invoice = $this->getById($id);

        if ( ! $invoice) {
            return false;
        }

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::INVOICE,
            $invoice->getId(),
        );

        UpdateSntBalanceJob::dispatch($invoice->getPayed() * -1);

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
