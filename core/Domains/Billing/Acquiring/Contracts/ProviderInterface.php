<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Contracts;

use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Exceptions\ProviderProcessException;

interface ProviderInterface
{
    public function hasFullConfig(): bool;

    /**
     * @throws ProviderProcessException
     */
    public function getQrLink(AcquiringEntity $acquiring): string;

    /**
     * @throws ProviderProcessException
     */
    public function getPaymentLink(AcquiringEntity $acquiring): string;
}
