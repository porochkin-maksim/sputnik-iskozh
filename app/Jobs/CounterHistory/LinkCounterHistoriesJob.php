<?php declare(strict_types=1);

namespace App\Jobs\CounterHistory;

use App\Services\Queue\QueueEnum;
use Core\Contracts\EventDispatcherInterface;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\CounterHistory\Events\CounterHistoriesLinked;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LinkCounterHistoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $olderHistoryId,
        private readonly int $newerHistoryId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(
        CounterHistoryService    $counterHistoryService,
        EventDispatcherInterface $eventDispatcher,
    ): void
    {
        $newerHistory = $counterHistoryService->getById($this->newerHistoryId);
        $olderHistory = $counterHistoryService->getById($this->olderHistoryId);

        if ( ! $olderHistory && ! $newerHistory) {
            return;
        }

        $newerHistory->setPreviousId($newerHistory->getPreviousId());
        $counterHistoryService->save($newerHistory);

        $eventDispatcher->dispatch(new CounterHistoriesLinked($newerHistory->getId()));
    }
}
