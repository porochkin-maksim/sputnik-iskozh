<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Listeners;

use Core\Domains\Billing\Payment\Events\PaymentCreatedEvent;
use Core\Domains\Billing\Payment\Jobs\NotifyAboutNewUnverifiedPaymentJob;

class PaymentCreatedListener
{
    public function handle(PaymentCreatedEvent $event): void
    {
        NotifyAboutNewUnverifiedPaymentJob::dispatch($event->paymentId);
    }
}
