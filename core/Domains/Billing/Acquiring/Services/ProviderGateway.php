<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Services;

use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Exceptions\ProviderProcessException;
use Core\Domains\Billing\Acquiring\Exceptions\UndefinedProviderException;

readonly class ProviderGateway
{
    public function __construct(
        private ProviderSelector $providerSelector,
    )
    {
    }

    /**
     * @throws ProviderProcessException
     * @throws UndefinedProviderException
     */
    public function getQrLink(AcquiringEntity $acquiring): string
    {
        return $this->providerSelector->getProviderService($acquiring->getProvider())->getQrLink($acquiring);
    }

    /**
     * @throws ProviderProcessException
     * @throws UndefinedProviderException
     */
    public function getPaymentLink(AcquiringEntity $acquiring): string
    {
        return $this->providerSelector->getProviderService($acquiring->getProvider())->getPaymentLink($acquiring);
    }
}
