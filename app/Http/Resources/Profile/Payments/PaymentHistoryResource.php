<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Payments;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Payment\PaymentEntity;

readonly class PaymentHistoryResource extends AbstractResource
{
    public function __construct(
        private PaymentEntity $payment,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $invoice = $this->payment->getInvoice();
        $period  = $invoice?->getPeriod();

        return [
            'id'            => $this->payment->getId(),
            'date'          => $this->formatDateTimeForRender($this->payment->getCreatedAt()),
            'cost'          => $this->payment->getCost(),
            'name'          => $this->payment->getName(),
            'invoiceName'   => $invoice?->getName(),
            'periodName'    => $period?->getName(),
            'accountNumber' => $this->payment->getAccountNumber(),
            'verified'      => $this->payment->isVerified(),
        ];
    }
}
