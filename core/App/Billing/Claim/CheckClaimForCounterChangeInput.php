<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

readonly class CheckClaimForCounterChangeInput
{
    public function __construct(
        public int $counterHistoryId,
    )
    {
    }
}
