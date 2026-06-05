<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Services;

use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;

class AcquiringFactory
{
    public function __construct(
        private readonly ProviderSelector $providerSelector,
    )
    {
    }

    public function makeDefault(): AcquiringEntity
    {
        return (new AcquiringEntity())
            ->setStatus(StatusEnum::NEW)
            ->setProvider($this->providerSelector->random());
    }
}
