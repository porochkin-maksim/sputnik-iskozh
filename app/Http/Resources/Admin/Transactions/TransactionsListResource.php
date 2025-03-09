<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Transactions;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Transaction\Collections\TransactionCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

readonly class TransactionsListResource extends AbstractResource
{
    public function __construct(
        private TransactionCollection $transactionCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [
            'transactions' => [],
            'historyUrl'   => HistoryChangesLocator::route(
                type: HistoryType::INVOICE,
                referenceType: HistoryType::TRANSACTION,
            ),
        ];

        foreach ($this->transactionCollection as $transaction) {
            $result['transactions'][] = new TransactionResource($transaction);
        }

        return $result;
    }
}
