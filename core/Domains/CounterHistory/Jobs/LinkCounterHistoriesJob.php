<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory\Jobs;

use Core\Domains\Billing\Jobs\CheckClaimForCounterChangeJob;
use App\Services\Queue\QueueEnum;
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
        CounterHistoryService $counterHistoryService,
    ): void
    {
        $newerHistory = $counterHistoryService->getById($this->newerHistoryId);
        $olderHistory = $counterHistoryService->getById($this->olderHistoryId);

        if ( ! $olderHistory && ! $newerHistory) {
            return;
        }

        $newerHistory->setPreviousId($newerHistory->getPreviousId());
        $counterHistoryService->save($newerHistory);

        dispatch(new CheckClaimForCounterChangeJob($newerHistory->getId()));
    }
}
