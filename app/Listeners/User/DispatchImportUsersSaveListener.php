<?php declare(strict_types=1);

namespace App\Listeners\User;

use App\Jobs\Infra\DbLock\LockedJob;
use App\Jobs\User\SaveImportedUsersJob;
use App\Services\Queue\QueueEnum;
use Core\Domains\User\Events\ImportUsersSaveRequested;

class DispatchImportUsersSaveListener
{
    public function handle(ImportUsersSaveRequested $event): void
    {
        $job = new LockedJob(
            SaveImportedUsersJob::class,
            [$event->users],
            $event->lockName,
        );
        $job->onQueue(QueueEnum::DEFAULT->value);

        dispatch_sync($job);
    }
}
