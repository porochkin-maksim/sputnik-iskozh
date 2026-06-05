<?php declare(strict_types=1);

namespace Core\Domains\HistoryChanges\Events;

use Core\Domains\HistoryChanges\HistoryChangesEntity;

readonly class HistoryChangesSaveRequested
{
    public function __construct(
        public HistoryChangesEntity $historyChanges,
    )
    {
    }
}
