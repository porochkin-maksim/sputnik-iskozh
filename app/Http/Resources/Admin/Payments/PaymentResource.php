<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Payments;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Invoices\InvoiceResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Models\AcquiringSearcher;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\HistoryChanges\HistoryType;
use lc;

readonly class PaymentResource extends AbstractResource
{
    public function __construct(
        private PaymentEntity $payment,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();

        $period = $this->payment->getInvoice()?->getPeriod();

        $hasAcquiring = false;
        if ($this->payment->getId()) {
            $hasAcquiring = app(AcquiringService::class)
                ->search((new AcquiringSearcher())
                        ->setPaymentId($this->payment->getId())
                        ->setStatus(StatusEnum::PAID)
                )
                ->getItems()
                ->first()?->getId();
        }

        return [
            'id'            => $this->payment->getId(),
            'name'          => $this->payment->getName(),
            'cost'          => $this->payment->getCost(),
            'comment'       => $this->payment->getComment(),
            'files'         => $this->payment->getFiles(),
            'created'       => $this->formatDateTimeForRender($this->payment->getCreatedAt()),
            'paid'          => $this->formatDateForRender($this->payment->getPaidAt()),
            'invoiceId'     => $this->payment->getInvoiceId(),
            'accountNumber' => $this->payment->getAccountNumber(),
            'accountId'     => $this->payment->getAccountId(),
            'invoice'       => $this->payment->getInvoice() ? new InvoiceResource($this->payment->getInvoice()) : null,
            'actions'       => [
                'view' => $access->can(PermissionEnum::PAYMENTS_VIEW),
                'edit' => $access->can(PermissionEnum::PAYMENTS_EDIT) && ! $period?->isClosed() && ! $hasAcquiring,
                'drop' => $access->can(PermissionEnum::PAYMENTS_DROP) && ! $period?->isClosed() && ! $hasAcquiring,
            ],
            'historyUrl'    => $this->payment->getId()
                ? HistoryChangesRoute::make(
                    referenceType: HistoryType::PAYMENT,
                    referenceId  : $this->payment?->getId(),
                ) : null,
        ];
    }
}
