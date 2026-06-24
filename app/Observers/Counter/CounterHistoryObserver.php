<?php declare(strict_types=1);

namespace App\Observers\Counter;

use App\Models\Counter\CounterHistory;
use App\Observers\AbstractObserver;
use Core\Domains\HistoryChanges\HistoryType;

class CounterHistoryObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::COUNTER;
    }

    protected function getPrimaryIdField(): ?string
    {
        return CounterHistory::COUNTER_ID;
    }

    protected function getReferenceHistoryType(): ?HistoryType
    {
        return HistoryType::COUNTER_HISTORY;
    }

    protected function getReferenceIdField(): ?string
    {
        return CounterHistory::ID;
    }

    protected function getPropertyTitles(): array
    {
        return CounterHistory::PROPERTIES_TO_TITLES;
    }
}
