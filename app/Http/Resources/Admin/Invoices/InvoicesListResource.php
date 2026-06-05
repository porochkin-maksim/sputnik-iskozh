<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Invoices;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Invoice\InvoiceCollection;

readonly class InvoicesListResource extends AbstractResource
{
    public function __construct(
        private InvoiceCollection $invoiceCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->invoiceCollection as $invoice) {
            $result[] = new InvoiceResource($invoice);
        }

        return $result;
    }
}
