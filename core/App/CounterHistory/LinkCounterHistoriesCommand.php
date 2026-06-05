<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Contracts\EventDispatcherInterface;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\CounterHistory\Events\CounterHistoriesLinked;

readonly class LinkCounterHistoriesCommand
{
    public function __construct(
        private CounterHistoryService    $counterHistoryService,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function execute(int $olderHistoryId, int $newerHistoryId): void
    {
        $newerHistory = $this->counterHistoryService->getById($newerHistoryId);
        $olderHistory = $this->counterHistoryService->getById($olderHistoryId);

        if ($olderHistory === null && $newerHistory === null) {
            return;
        }

        if ($newerHistory === null) {
            return;
        }

        $newerHistory->setPreviousId($newerHistory->getPreviousId());
        $this->counterHistoryService->save($newerHistory);

        $this->eventDispatcher->dispatch(new CounterHistoriesLinked($newerHistory->getId()));
    }
}
