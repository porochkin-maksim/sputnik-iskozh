<?php declare(strict_types=1);

namespace Core\Domains\Counter\Jobs;

use Core\Domains\Billing\Jobs\CheckTransactionForCounterChangeJob;
use Core\Domains\Counter\CounterLocator;
use Core\Queue\QueueEnum;
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

    public function handle(): void
    {
        $newerHistory = CounterLocator::CounterHistoryService()->getById($this->newerHistoryId);
        $olderHistory = CounterLocator::CounterHistoryService()->getById($this->olderHistoryId);

        if ( ! $olderHistory && ! $newerHistory) {
            return;
        }

        $newerHistory->setPreviousId($newerHistory->getPreviousId());
        CounterLocator::CounterHistoryService()->save($newerHistory);

        dispatch(new CheckTransactionForCounterChangeJob($newerHistory->getId()));
    }
}