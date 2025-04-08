<?php declare(strict_types=1);

namespace Core\Domains\Counter\Jobs;

use Carbon\Carbon;
use Core\Domains\Account\Enums\AccountIdEnum;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\OptionLocator;
use Core\Queue\QueueEnum;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AutoIncrementingCounterHistoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue(QueueEnum::LOW->value);
    }

    public function handle(): void
    {
        $option = OptionLocator::OptionService()->getByType(OptionEnum::COUNTER_READING_DAY)->getData();
        /** @var CounterReadingDay $option */
        if ( ! $option?->getDay() || $option?->getDay() !== Carbon::now()->day) {
            return;
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setWithHistory()
            ->setHasIncrement()
        ;
        $counters = CounterLocator::CounterService()->search($counterSearcher)->getItems();

        DB::beginTransaction();
        try {
            foreach ($counters as $counter) {
                if ($counter->getIncrement() === null || $counter->getAccountId() === AccountIdEnum::SNT->value) {
                    continue;
                }

                $lastHistory = $counter->getHistoryCollection()->first();

                if ($lastHistory && $lastHistory->getDate()?->isCurrentMonth()) {
                    return;
                }

                $history = CounterLocator::CounterHistoryFactory()->makeDefault();
                $history
                    ->setCounterId($counter->getId())
                    ->setValue((int) $lastHistory?->getValue() + (int) $counter->getIncrement())
                ;

                $history = CounterLocator::CounterHistoryService()->save($history);

                HistoryChangesLocator::HistoryChangesService()->writeToHistory(
                    Event::COMMON,
                    HistoryType::COUNTER,
                    $counter->getCounterId(),
                    HistoryType::COUNTER_HISTORY,
                    $history->getId(),
                    text: sprintf("Автоматическое прирощение показаний: на  %sкВт", $counter->getIncrement()),
                );
            }

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}