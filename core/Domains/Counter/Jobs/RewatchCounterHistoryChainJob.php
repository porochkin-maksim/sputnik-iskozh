<?php declare(strict_types=1);

namespace Core\Domains\Counter\Jobs;

use App\Models\Counter\CounterHistory;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Jobs\CheckClaimForCounterChangeJob;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Queue\DispatchIfNeededTrait;
use Core\Queue\QueueEnum;
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

    public function process(): void
    {
        $counterHistorySearcher = new CounterHistorySearcher();
        $counterHistorySearcher->setCounterId($this->counterId)
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_ASC)
        ;

        $histories = CounterLocator::CounterHistoryService()->search($counterHistorySearcher)->getItems();

        $previous = null;
        foreach ($histories as $history) {
            if ( ! $previous) {
                $history->setPreviousId(null);
                $history->setPreviousValue(null);
            } else {
                $history->setPreviousId($previous->getId());
                $history->setPreviousValue($previous->getValue());
            }

            $history = CounterLocator::CounterHistoryService()->save($history);
            dispatch_sync(new CheckClaimForCounterChangeJob($history->getId()));
            $previous = $history;
        }
    }
}
