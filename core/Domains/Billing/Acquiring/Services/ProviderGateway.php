<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Services;

use Core\Domains\Billing\Acquiring\Exceptions\ProviderProcessException;
use Core\Domains\Billing\Acquiring\Exceptions\UndefinedProviderException;
use Core\Domains\Billing\Acquiring\Models\AcquiringDTO;

readonly class ProviderGateway
{
    /**
     * @throws ProviderProcessException
     * @throws UndefinedProviderException
     */
    public function getQrLink(AcquiringDTO $acquiring): string
    {
        return ProviderSelector::getProviderService($acquiring->getProvider())->getQrLink($acquiring);
    }

    /**
     * @throws ProviderProcessException
     * @throws UndefinedProviderException
     */
    public function getPaymentLink(AcquiringDTO $acquiring): string
    {
        return ProviderSelector::getProviderService($acquiring->getProvider())->getPaymentLink($acquiring);
    }
}
