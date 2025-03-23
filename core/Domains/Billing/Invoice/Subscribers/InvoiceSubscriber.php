<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Subscribers;

use Core\Domains\Billing\Invoice\Events\InvoiceCreatedEvent;
use Core\Domains\Billing\Invoice\Listeners\InvoiceCreatedListener;
use Illuminate\Events\Dispatcher;

class InvoiceSubscriber
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(InvoiceCreatedEvent::class, InvoiceCreatedListener::class);
    }
}
