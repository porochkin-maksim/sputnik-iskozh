<?php declare(strict_types=1);

namespace App\Listeners\HistoryChanges;

use Core\Domains\HistoryChanges\Events\HistoryChangesSaveRequested;
use App\Jobs\HistoryChanges\CreateHistoryJob;

class DispatchCreateHistoryJobListener
{
    public function handle(HistoryChangesSaveRequested $event): void
    {
        dispatch(new CreateHistoryJob($event->historyChanges));
    }
}
