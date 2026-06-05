<?php declare(strict_types=1);

namespace Core\App\HistoryChanges;

use Core\Domains\HistoryChanges\HistoryChangesEntity;

readonly class CreateHistoryInput
{
    public function __construct(
        public HistoryChangesEntity $historyChanges,
    )
    {
    }
}
