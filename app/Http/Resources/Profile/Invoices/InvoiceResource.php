<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Invoices;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Periods\PeriodResource;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;

readonly class InvoiceResource extends AbstractResource
{
    public function __construct(
        private InvoiceDTO $invoice,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'      => $this->invoice->getId(),
            'cost'    => $this->invoice->getCost(),
            'payed'   => $this->invoice->getPayed(),
            'delta'   => $this->invoice->getDelta(),
            'period'  => $this->invoice->getPeriod() ? new PeriodResource($this->invoice->getPeriod()) : null,
            'account' => $this->invoice->getAccount() ? new AccountResource($this->invoice->getAccount()) : null,
        ];
    }
} 