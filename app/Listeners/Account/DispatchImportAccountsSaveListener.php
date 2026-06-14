<?php declare(strict_types=1);

namespace App\Listeners\Account;

use App\Jobs\Account\SaveImportedAccountsJob;
use App\Jobs\Infra\DbLock\LockedJob;
use App\Services\Queue\QueueEnum;
use Core\Domains\Account\Events\ImportAccountsSaveRequested;

class DispatchImportAccountsSaveListener
{
    public function handle(ImportAccountsSaveRequested $event): void
    {
        $job = new LockedJob(
            SaveImportedAccountsJob::class,
            [$event->accounts],
            $event->lockName,
        );
        $job->onQueue(QueueEnum::DEFAULT->value);

        dispatch_sync($job);
    }
}
