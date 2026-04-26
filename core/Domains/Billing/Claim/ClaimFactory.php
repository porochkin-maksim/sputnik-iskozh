<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim;

readonly class ClaimFactory
{
    public function makeDefault(): ClaimEntity
    {
        return (new ClaimEntity())
            ->setTariff(0.00)
            ->setCost(0.00)
            ->setPaid(0.00);
    }
}
