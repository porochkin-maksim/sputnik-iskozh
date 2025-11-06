<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Providers;

use Core\Domains\Billing\Acquiring\Exceptions\ProviderProcessException;
use Core\Domains\Billing\Acquiring\Models\AcquiringDTO;

interface ProviderInterface
{
    public function hasFullConfig(): bool;

    /**
     * @throws ProviderProcessException
     */
    public function getQrLink(AcquiringDTO $acquiring): string;

    /**
     * @throws ProviderProcessException
     */
    public function getPaymentLink(AcquiringDTO $acquiring): string;
}
