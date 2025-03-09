<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Payments;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Invoices\InvoiceResource;
use Core\Domains\Billing\payment\Models\paymentDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Resources\RouteNames;

readonly class PaymentResource extends AbstractResource
{
    public function __construct(
        private PaymentDTO $payment,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->payment->getId(),
            'cost'       => $this->payment->getCost(),
            'comment'    => $this->payment->getComment(),
            'created'    => $this->formatCreatedAt($this->payment->getCreatedAt()),
            'invoice'    => $this->payment->getInvoice() ? new InvoiceResource($this->payment->getInvoice()) : null,
            'actions'    => [
                'drop' => ! $this->payment->getId(),
            ],
            'historyUrl' => $this->payment->getId()
                ? HistoryChangesLocator::route(
                    type: HistoryType::INVOICE,
                    referenceType: HistoryType::PAYMENT,
                    referenceId: $this->payment->getId(),
                ) : null,
        ];
    }
}
