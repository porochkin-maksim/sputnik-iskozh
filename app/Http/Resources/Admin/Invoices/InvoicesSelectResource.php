<?php

namespace App\Http\Resources\Admin\Invoices;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectOptionResource;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Core\Enums\DateTimeFormat;
use Core\Services\Money\MoneyService;

readonly class InvoicesSelectResource extends AbstractResource
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
            $name     = sprintf('№%s %s %s/%s от %s',
                $invoice->getId(),
                $invoice->getType()?->name(),
                number_format($invoice->getPayed(), 2, ',', ' '),
                MoneyService::parse($invoice->getCost()),
                $invoice->getCreatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
            );
            $result[] = new SelectOptionResource($invoice->getId(), $name);
        }

        return $result;
    }
}