<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory\Jobs;

use App\Locators\CounterLocator;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Carbon\Carbon;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\OptionService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AutoIncrementingCounterHistoriesJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue(QueueEnum::LOW->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::AUTO_INCREMENTING_COUNTER_HISTORIES_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return null;
    }

    public function process(
        CounterHistoryService $counterHistoryService,
        CounterHistoryFactory $counterHistoryFactory,
        HistoryChangesService $historyChangesService,
        OptionService $optionService,
    ): void
    {
        $option = $optionService->getByType(OptionEnum::COUNTER_READING_DAY)->getData();
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

                $lastHistory = $counterHistoryService->getLastByCounterId($counter->getId());

                if ($lastHistory && $lastHistory->getDate()?->isCurrentMonth()) {
                    continue;
                }

                $history = $counterHistoryFactory->makeDefault();
                $history
                    ->setCounterId($counter->getId())
                    ->setValue((int) $lastHistory?->getValue() + (int) $counter->getIncrement())
                    ->setIsVerified($lastHistory->isVerified())
                ;

                $history = $counterHistoryService->save($history);

                $historyChangesService->writeToHistory(
                    Event::COMMON,
                    HistoryType::COUNTER,
                    $history->getCounterId(),
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
