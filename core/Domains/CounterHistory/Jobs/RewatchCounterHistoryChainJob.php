<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory\Jobs;

use App\Models\Counter\CounterHistory;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Core\Domains\Billing\Jobs\CheckClaimForCounterChangeJob;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Repositories\SearcherInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RewatchCounterHistoryChainJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $counterId,
    )
    {
        $this->onQueue(QueueEnum::LOW->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::REWATCH_COUNTER_HISTORY_CHAIN_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->counterId;
    }

    public function process(
        CounterHistoryService $counterHistoryService,
    ): void
    {
        $counterHistorySearcher = new CounterHistorySearcher();
        $counterHistorySearcher->setCounterId($this->counterId)
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_ASC)
        ;

        $histories = $counterHistoryService->search($counterHistorySearcher)->getItems();

        $previous = null;
        foreach ($histories as $history) {
            if ( ! $previous) {
                $history->setPreviousId(null);
                $history->setPreviousValue(null);
            } else {
                $history->setPreviousId($previous->getId());
                $history->setPreviousValue($previous->getValue());
            }

            $history = $counterHistoryService->save($history);
            dispatch_sync(new CheckClaimForCounterChangeJob($history->getId()));
            $previous = $history;
        }
    }
}
