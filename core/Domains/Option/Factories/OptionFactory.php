<?php declare(strict_types=1);

namespace Core\Domains\Option\Factories;

use App\Models\Option;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\OptionDTO;

readonly class OptionFactory
{
    public function makeModelFromDto(OptionDTO $dto, ?Option $model = null): Option
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Option::make();
        }

        $result->forceFill([
            'id' => $dto->getId(),
        ]);

        return $result->fill([
            Option::TYPE => $dto->getType()?->value ?: $dto->getId(),
            Option::DATA => $dto->getData(),
        ]);
    }

    public function makeDtoFromObject(Option $model): OptionDTO
    {
        $result = new OptionDTO();

        $result
            ->setId($model->id)
            ->setType(OptionEnum::tryFrom($model->type))
            ->setData($model->data);

        return $result;
    }

    public function make(OptionEnum $model): OptionDTO
    {
        $result = new OptionDTO();
        $result
            ->setId($model->value)
            ->setType($model)
            ->setData($model->default());

        return $result;
    }
}