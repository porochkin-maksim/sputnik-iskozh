<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Service\ServiceCollection;

readonly class ClaimFormData
{
    /**
     * @param array<int, string> $servicesSelect
     */
    public function __construct(
        public array $servicesSelect,
        public ClaimEntity $claim,
        public ServiceCollection $services,
    )
    {
    }
}
