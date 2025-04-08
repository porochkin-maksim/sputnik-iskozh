<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Payments;

use lc;
use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Invoices\InvoiceResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\payment\Models\paymentDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class PaymentResource extends AbstractResource
{
    public function __construct(
        private PaymentDTO $payment,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();

        return [
            'id'         => $this->payment->getId(),
            'name'       => $this->payment->getName(),
            'cost'       => $this->payment->getCost(),
            'comment'    => $this->payment->getComment(),
            'files'      => $this->payment->getFiles(),
            'created'    => $this->formatCreatedAt($this->payment->getCreatedAt()),
            'invoiceId'  => $this->payment->getInvoiceId(),
            'accountId'  => $this->payment->getAccountId(),
            'invoice'    => $this->payment->getInvoice() ? new InvoiceResource($this->payment->getInvoice()) : null,
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::PAYMENTS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::PAYMENTS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::PAYMENTS_DROP),
            ],
            'historyUrl' => $this->payment->getId()
                ? HistoryChangesLocator::route(
                    referenceType: HistoryType::PAYMENT,
                    referenceId  : $this->payment?->getId(),
                ) : null,
        ];
    }
}
