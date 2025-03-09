<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Repositories;

use App\Models\Billing\Invoice;
use Core\Db\RepositoryTrait;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;

class InvoiceRepository
{
    private const TABLE = Invoice::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Invoice::class;
    }

    public function getById(?int $id): ?Invoice
    {
        /** @var ?Invoice $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function save(Invoice $invoice): Invoice
    {
        $invoice->save();

        return $invoice;
    }
}
