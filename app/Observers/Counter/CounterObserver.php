<?php declare(strict_types=1);

namespace App\Observers\Counter;

use App\Models\Counter\Counter;
use App\Observers\AbstractObserver;
use Core\Domains\HistoryChanges\HistoryType;

class CounterObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::COUNTER;
    }

    protected function getPropertyTitles(): array
    {
        return Counter::PROPERTIES_TO_TITLES;
    }
}
