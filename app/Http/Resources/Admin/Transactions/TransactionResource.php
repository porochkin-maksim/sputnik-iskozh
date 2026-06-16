<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Transactions;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Transaction\TransactionEntity;

readonly class TransactionResource extends AbstractResource
{
    public function __construct(
        private TransactionEntity $transaction,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $claim = $this->transaction->getClaim();

        $name = 'Аванс';
        if ($claim) {
            $name = $claim->getName();
            if ( ! $name) {
                $service = $claim->getService();
                $name    = $service?->getName() ?: 'Услуга №' . $claim->getId();
            }
        }

        return [
            'id'        => $this->transaction->getId(),
            'paymentId' => $this->transaction->getPaymentId(),
            'claimId'   => $this->transaction->getClaimId(),
            'invoiceId' => $claim?->getInvoiceId(),
            'name'      => $name,
            'cost'      => $this->transaction->getCost(),
            'createdAt' => $this->formatDateTimeForRender($this->transaction->getCreatedAt()),
        ];
    }
}
