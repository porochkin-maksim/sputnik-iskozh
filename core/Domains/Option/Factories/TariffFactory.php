<?php declare(strict_types=1);

namespace Core\Domains\Option\Factories;

use App\Models\Option;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\Tariff\Tariff;

readonly class TariffFactory extends OptionFactory
{
    public function makeDtoFromObject(Option $option): Tariff
    {
        $result = new Tariff();

        $result
            ->setId($option->id)
            ->setType(OptionEnum::tryFrom($option->type))
            ->setData($option->data);

        return $result;
    }
}