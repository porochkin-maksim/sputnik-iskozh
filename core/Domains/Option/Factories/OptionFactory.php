<?php declare(strict_types=1);

namespace Core\Domains\Option\Factories;

use App\Models\Option;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\OptionDTO;

readonly class OptionFactory
{
    public function makeModelFromDto(OptionDTO $dto, ?Option $option = null): Option
    {
        if ($option) {
            $result = $option;
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

    public function makeDtoFromObject(Option $option): OptionDTO
    {
        $result = new OptionDTO();

        $result
            ->setId($option->id)
            ->setType(OptionEnum::tryFrom($option->type))
            ->setData($option->data);

        return $result;
    }

    public function make(OptionEnum $option): OptionDTO
    {
        $result = new OptionDTO();
        $result
            ->setId($option->value)
            ->setType($option)
            ->setData($option->default());

        return $result;
    }
}