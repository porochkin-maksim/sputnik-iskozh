<?php declare(strict_types=1);

namespace App\Observers\Counter;

use App\Models\Counter\Counter;
use App\Observers\AbstractObserver;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;

class CounterObserver extends AbstractObserver
{
    public function created(Counter $item): void
    {
        $changes = $this->makeChanges($item);

        $this->historyChangesService->logChanges(
            Event::CREATE,
            HistoryType::COUNTER,
            $changes,
            $item->id,
        );
    }

    public function updated(Counter $item): void
    {
        $changes = $this->makeChanges($item);

        $this->historyChangesService->logChanges(
            Event::UPDATE,
            HistoryType::COUNTER,
            $changes,
            $item->id,
        );
    }

    public function deleted(Counter $item): void
    {
        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::COUNTER,
            $item->id,
        );
    }

    public function forceDeleted(Counter $item): void
    {
        $this->deleted($item);
    }

    public function restored(Counter $item): void
    {
        //
    }

    protected function getPropertyTitles(): array
    {
        return Counter::PROPERTIES_TO_TITLES;
    }
}
