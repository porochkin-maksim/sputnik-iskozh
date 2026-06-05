<?php declare(strict_types=1);

namespace App\Listeners\Billing;

use App\Services\Queue\QueueEnum;
use Core\Domains\Billing\Events\ImportPaymentsSaveRequested;
use App\Jobs\Billing\SaveImportPaymentsJob;
use App\Jobs\Infra\DbLock\LockedJob;

class DispatchImportPaymentsSaveListener
{
    public function handle(ImportPaymentsSaveRequested $event): void
    {
        $job = new LockedJob(
            SaveImportPaymentsJob::class,
            [$event->paymentsData],
            $event->lockName,
        );
        $job->onQueue(QueueEnum::DEFAULT->value);

        dispatch($job);
    }
}
