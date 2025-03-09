<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Listeners;

use Core\Domains\Billing\Period\Events\PeriodCreatedEvent;
use Core\Domains\Billing\Period\Jobs\CreateMainServicesJob;


class PeriodCreatedListener
{
    public function handle(PeriodCreatedEvent $event): void
    {
        CreateMainServicesJob::dispatch($event->periodId);
    }
}
