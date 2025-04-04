<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Transactions;

use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\transaction\Models\transactionDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class TransactionResource extends AbstractResource
{
    public function __construct(
        private TransactionDTO $transaction,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $name   = $this->transaction->getName();
        if ( ! $name) {
            $name = $this->transaction->getService()?->getName();
        }

        return [
            'id'         => $this->transaction->getId(),
            'tariff'     => $this->transaction->getTariff(),
            'cost'       => $this->transaction->getCost(),
            'payed'      => $this->transaction->getPayed(),
            'delta'      => $this->transaction->getCost() - $this->transaction->getPayed(),
            'serviceId'  => $this->transaction->getServiceId(),
            'service'    => $name,
            'name'       => $this->transaction->getName(),
            'created'    => $this->formatCreatedAt($this->transaction->getCreatedAt()),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::TRANSACTIONS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::TRANSACTIONS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::TRANSACTIONS_DROP),
            ],
            'historyUrl' => $this->transaction->getId()
                ? HistoryChangesLocator::route(
                    type         : HistoryType::INVOICE,
                    primaryId    : $this->transaction->getInvoiceId(),
                    referenceType: HistoryType::TRANSACTION,
                    referenceId  : $this->transaction->getId(),
                ) : null,
        ];
    }
}
