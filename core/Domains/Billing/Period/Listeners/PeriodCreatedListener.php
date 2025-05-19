<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Listeners;

use Core\Domains\Billing\Jobs\CreateMainServicesJob;
use Core\Domains\Billing\Period\Events\PeriodCreatedEvent;

class PeriodCreatedListener
{
    public function handle(PeriodCreatedEvent $event): void
    {
        CreateMainServicesJob::dispatch($event->periodId);
    }
}
