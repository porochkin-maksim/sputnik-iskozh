<?php declare(strict_types=1);

namespace Core\Domains\Billing\TransactionToObject\Factories;

use App\Models\Billing\TransactionToObject;
use Core\Domains\Billing\TransactionToObject\Enums\TransactionObjectTypeEnum;
use Core\Domains\Billing\TransactionToObject\Models\TransactionToObjectDTO;

class TransactionToObjectFactory
{
    public function makeDefault(): TransactionToObjectDTO
    {
        return new TransactionToObjectDTO();
    }

    public function makeModelFromDto(TransactionToObjectDTO $dto, ?TransactionToObject $model = null): TransactionToObject
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = TransactionToObject::make();
        }

        return $result->fill([
            TransactionToObject::TRANSACTION_ID => $dto->getTransactionId(),
            TransactionToObject::REFERENCE_ID   => $dto->getReferenceId(),
            TransactionToObject::TYPE           => $dto->getType()->value,
        ]);
    }

    public function makeDtoFromObject(TransactionToObject $model): TransactionToObjectDTO
    {
        $result = new TransactionToObjectDTO();

        $result
            ->setTransactionId($model->transaction_id)
            ->setReferenceId($model->reference_id)
            ->setType(TransactionObjectTypeEnum::tryFrom($model->type))
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
        ;

        return $result;
    }
}