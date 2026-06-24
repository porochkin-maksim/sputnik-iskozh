<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use Carbon\Carbon;

readonly class PaymentFactory
{
    public function makeDefault(): PaymentEntity
    {
        return (new PaymentEntity())
            ->setModerated(false)
            ->setVerified(false)
            ->setCost(0.00)
            ->setPaidAt(Carbon::now());
    }
}
