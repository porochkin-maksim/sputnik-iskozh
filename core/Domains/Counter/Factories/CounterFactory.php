<?php declare(strict_types=1);

namespace Core\Domains\Counter\Factories;

use App\Models\Counter;
use Core\Domains\Counter\Enums\TypeEnum;
use Core\Domains\Counter\Models\CounterDTO;

readonly class CounterFactory
{
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
            Counter::TYPE       => $dto->getType()?->value,
            Counter::ACCOUNT_ID => $dto->getAccountId(),
            Counter::NUMBER     => $dto->getNumber(),
            Counter::IS_PRIMARY => $dto->isPrimary(),
        ]);
    }

    public function makeDtoFromObject(Counter $model): CounterDTO
    {
        $result = new CounterDTO();

        $result
            ->setId($model->id)
            ->setType(TypeEnum::tryFrom($model->type))
            ->setAccountId($model->account_id)
            ->setNumber($model->number)
            ->setIsPrimary($model->is_primary)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        return $result;
    }

    public function make(TypeEnum $type): CounterDTO
    {
        $result = new CounterDTO();
        $result->setType($type);

        return $result;
    }
}