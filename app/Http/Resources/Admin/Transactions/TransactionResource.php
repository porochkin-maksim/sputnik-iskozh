<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Transactions;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\transaction\Models\transactionDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Resources\RouteNames;

readonly class TransactionResource extends AbstractResource
{
    public function __construct(
        private TransactionDTO $transaction,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $name = $this->transaction->getName();
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
            'actions'    => [
                'drop' => ! $this->transaction->getId(),
            ],
            'historyUrl' => $this->transaction->getId()
                ? HistoryChangesLocator::route(
                    type: HistoryType::INVOICE,
                    primaryId: $this->transaction->getInvoiceId(),
                    referenceType: HistoryType::TRANSACTION,
                    referenceId: $this->transaction->getId(),
                ) : null,
        ];
    }
}
