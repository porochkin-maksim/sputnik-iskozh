<?php declare(strict_types=1);

namespace Core\Domains\Counter\Factories;

use App\Models\Counter;
use Core\Domains\Counter\Enums\TypeEnum;
use Core\Domains\Counter\Models\CounterDTO;

readonly class CounterFactory
{
    public function makeModelFromDto(CounterDTO $dto, ?Counter $counter = null): Counter
    {
        if ($counter) {
            $result = $counter;
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

    public function makeDtoFromObject(Counter $counter): CounterDTO
    {
        $result = new CounterDTO();

        $result
            ->setId($counter->id)
            ->setType(TypeEnum::tryFrom($counter->type))
            ->setAccountId($counter->account_id)
            ->setNumber($counter->number)
            ->setIsPrimary($counter->is_primary);

        return $result;
    }

    public function make(TypeEnum $type): CounterDTO
    {
        $result = new CounterDTO();
        $result->setType($type);

        return $result;
    }
}