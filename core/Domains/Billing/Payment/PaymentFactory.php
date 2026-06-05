<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

readonly class PaymentFactory
{
    public function makeDefault(): PaymentEntity
    {
        return (new PaymentEntity())
            ->setModerated(false)
            ->setVerified(false)
            ->setCost(0.00);
    }
}
