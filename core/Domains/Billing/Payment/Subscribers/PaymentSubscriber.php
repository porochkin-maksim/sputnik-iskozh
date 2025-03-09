<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Subscribers;

use Core\Domains\Billing\Payment\Events\PaymentsUpdatedEvent;
use Core\Domains\Billing\Payment\Listeners\PaymentUpdatedListener;
use Illuminate\Events\Dispatcher;

class PaymentSubscriber
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(PaymentsUpdatedEvent::class, PaymentUpdatedListener::class);
    }
}
