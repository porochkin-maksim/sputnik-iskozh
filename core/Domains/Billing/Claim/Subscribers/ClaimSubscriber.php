<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Subscribers;

use Core\Domains\Billing\Claim\Events\ClaimDeletedEvent;
use Core\Domains\Billing\Claim\Events\ClaimsUpdatedEvent;
use Core\Domains\Billing\Claim\Listeners\ClaimDeletedListener;
use Core\Domains\Billing\Claim\Listeners\ClaimsUpdatedListener;
use Illuminate\Events\Dispatcher;

class ClaimSubscriber
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(ClaimsUpdatedEvent::class, ClaimsUpdatedListener::class);
        $events->listen(ClaimDeletedEvent::class, ClaimDeletedListener::class);
    }
}
