<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Transactions;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Transaction\Models\TransactionDTO;

readonly class TransactionResource extends AbstractResource
{
    public function __construct(
        private TransactionDTO $transaction,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $name   = $this->transaction->getName();
        if ( ! $name) {
            $name = $this->transaction->getService()?->getName();
        }

        return [
            'id'         => $this->transaction->getId(),
            'tariff'     => $this->transaction->getTariff(),
            'cost'       => $this->transaction->getCost(),
            'payed'      => $this->transaction->getPayed(),
            'serviceId'  => $this->transaction->getServiceId(),
            'service'    => $name,
            'name'       => $this->transaction->getName(),
            'created'    => $this->formatCreatedAt($this->transaction->getCreatedAt()),
        ];
    }
}