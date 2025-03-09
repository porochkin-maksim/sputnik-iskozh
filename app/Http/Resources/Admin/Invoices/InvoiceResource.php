<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Invoices;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Resources\RouteNames;

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
            'id'            => $this->invoice->getId(),
            'periodId'      => $this->invoice->getPeriodId(),
            'periodName'    => $this->invoice->getPeriod()?->getName(),
            'accountId'     => $this->invoice->getAccountId(),
            'accountNumber' => $this->invoice->getAccount()?->getNumber(),
            'type'          => $this->invoice->getType()?->value,
            'typeName'      => $this->invoice->getType()?->name(),
            'cost'          => $this->invoice->getCost(),
            'payed'         => $this->invoice->getPayed(),
            'isPayed'       => $this->invoice->isPayed(),
            'created'       => $this->formatCreatedAt($this->invoice->getCreatedAt()),
            'actions'       => [
                'drop' => ! $this->invoice->getId(),
            ],
            'viewUrl'       => $this->invoice->getId() ? route(RouteNames::ADMIN_INVOICE_VIEW, ['id' => $this->invoice->getId()]) : null,
            'historyUrl'    => $this->invoice->getId() ? route(RouteNames::HISTORY_CHANGES, [
                'type'      => HistoryType::INVOICE,
                'primaryId' => $this->invoice->getId(),
            ]) : null,
        ];
    }
}
