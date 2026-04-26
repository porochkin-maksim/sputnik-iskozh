<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice;

use Core\Repositories\SearcherInterface;

interface InvoiceRepositoryInterface
{
    public function search(SearcherInterface $searcher): InvoiceSearchResponse;

    public function save(InvoiceEntity $invoice): InvoiceEntity;

    public function getById(?int $id): ?InvoiceEntity;

    public function getByIds(array $ids): InvoiceSearchResponse;

    public function deleteById(?int $id): bool;

    public function getAccountIdsWithoutRegularInvoice(int $periodId): array;
}
