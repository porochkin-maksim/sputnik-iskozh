<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $paymentId,
    )
    {
    }
}
