<?php declare(strict_types=1);

namespace Core\Domains\Counter\Factories;

use App\Models\Counter\Counter;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Enums\CounterTypeEnum;
use Core\Domains\Counter\Models\CounterDTO;

class CounterFactory
{
    public function makeDefault(): CounterDTO
    {
        return $this->make(CounterTypeEnum::ELECTRICITY)
            ->setIsInvoicing(false)
            ->setIncrement(0)
        ;
    }

    public function makeModelFromDto(CounterDTO $dto, ?Counter $model = null): Counter
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Counter::make();
        }

        $result->forceFill([
            'id' => $dto->getId(),
        ]);

        return $result->fill([
            Counter::TYPE         => $dto->getType()?->value,
            Counter::ACCOUNT_ID   => $dto->getAccountId(),
            Counter::NUMBER       => $dto->getNumber(),
            Counter::IS_INVOICING => $dto->isInvoicing(),
            Counter::INCREMENT    => $dto->getIncrement(),
        ]);
    }

    public function makeDtoFromObject(Counter $model): CounterDTO
    {
        $result = new CounterDTO();

        $result
            ->setId($model->id)
            ->setType(CounterTypeEnum::tryFrom($model->type))
            ->setAccountId($model->account_id)
            ->setNumber($model->number)
            ->setIsInvoicing($model->is_invoicing)
            ->setIncrement($model->increment)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        if (isset($model->getRelations()[Counter::HISTORY])) {
            $result->setHistoryCollection(CounterLocator::CounterHistoryFactory()->makeDtoFromObjects($model->getRelation(Counter::HISTORY)));
        }

        if (isset($model->getRelations()[Counter::ACCOUNT])) {
            $result->setAccount(AccountLocator::AccountFactory()->makeDtoFromObject($model->getRelation(Counter::ACCOUNT)));
        }

        return $result;
    }

    public function make(CounterTypeEnum $type): CounterDTO
    {
        $result = new CounterDTO();
        $result->setType($type);

        return $result;
    }
}