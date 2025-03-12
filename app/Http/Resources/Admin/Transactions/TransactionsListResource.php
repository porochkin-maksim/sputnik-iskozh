<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Transactions;

use app;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Transaction\Collections\TransactionCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class TransactionsListResource extends AbstractResource
{
    public function __construct(
        private TransactionCollection $transactionCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = app::roleDecorator();
        $result = [
            'transactions' => [],
            'historyUrl'   => HistoryChangesLocator::route(
                type         : HistoryType::INVOICE,
                referenceType: HistoryType::TRANSACTION,
            ),
            'actions'      => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::TRANSACTIONS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::TRANSACTIONS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::TRANSACTIONS_DROP),
            ],
        ];

        foreach ($this->transactionCollection as $transaction) {
            $result['transactions'][] = new TransactionResource($transaction);
        }

        return $result;
    }
}
