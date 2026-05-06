<?php declare(strict_types=1);

namespace Core\App\HistoryChanges;

use Core\Domains\HistoryChanges\HistoryChangesEntity;

readonly class CreateHistoryCommand
{
    public function __construct(
        public HistoryChangesEntity $historyChanges,
    )
    {
    }
}
