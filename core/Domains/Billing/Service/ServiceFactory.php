<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service;

readonly class ServiceFactory
{
    public function makeDefault(): ServiceEntity
    {
        return $this->makeEmpty()
            ->setType(ServiceTypeEnum::OTHER)
            ->setName('')
            ->setIsActive(true)
            ->setCost(0);
    }

    public function makeEmpty(): ServiceEntity
    {
        return new ServiceEntity();
    }
}
