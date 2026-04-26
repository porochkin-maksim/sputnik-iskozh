<?php declare(strict_types=1);

namespace Core\Domains\Counter;

class CounterFactory
{
    public function makeDefault(): CounterEntity
    {
        return $this->make(CounterTypeEnum::ELECTRICITY)
            ->setIsInvoicing(false)
            ->setIncrement(0);
    }

    public function make(CounterTypeEnum $type): CounterEntity
    {
        return (new CounterEntity())->setType($type);
    }
}
