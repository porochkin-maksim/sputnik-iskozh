<?php declare(strict_types=1);

namespace Core\Domains\Billing\TransactionToObject\Services;

use App\Models\Billing\TransactionToObject;
use Core\Domains\Billing\Transaction\Models\TransactionDTO;
use Core\Domains\Billing\Transaction\TransactionLocator;
use Core\Domains\Billing\TransactionToObject\Enums\TransactionObjectTypeEnum;

readonly class TransactionToObjectService
{
    public function create(TransactionDTO $transaction, ?int $referenceId, TransactionObjectTypeEnum $type): void
    {
        TransactionToObject::make([
            TransactionToObject::TRANSACTION_ID => $transaction->getId(),
            TransactionToObject::REFERENCE_ID   => $referenceId,
            TransactionToObject::TYPE           => $type->value,
        ])->save();
    }

    public function getByReference(TransactionObjectTypeEnum $type, int $referenceId): ?TransactionDTO
    {
        $transactionId = TransactionToObject::where(TransactionToObject::TYPE, $type->value)
            ->where(TransactionToObject::REFERENCE_ID, $referenceId)
            ->value(TransactionToObject::TRANSACTION_ID);

        return TransactionLocator::TransactionService()->getById($transactionId);
    }

    public function hasRelations(TransactionDTO $transaction): bool
    {
        return (bool) TransactionToObject::where(TransactionToObject::TRANSACTION_ID, $transaction->getId())->first();
    }

    public function drop(TransactionDTO $transaction): void
    {
        TransactionToObject::where(TransactionToObject::TRANSACTION_ID, $transaction->getId())->delete();
    }
}
