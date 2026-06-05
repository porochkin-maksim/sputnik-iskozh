<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Carbon\Carbon;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\OptionService;

readonly class AutoIncrementingCounterHistoriesCommand
{
    public function __construct(
        private CounterService        $counterService,
        private CounterHistoryService $counterHistoryService,
        private CounterHistoryFactory $counterHistoryFactory,
        private HistoryChangesService $historyChangesService,
        private OptionService         $optionService,
    )
    {
    }

    public function execute(): void
    {
        $option = $this->optionService->getByType(OptionEnum::COUNTER_READING_DAY)->getData();
        /** @var CounterReadingDay $option */
        if ( ! $option?->getDay() || $option?->getDay() !== Carbon::now()->day) {
            return;
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setWithHistory()
            ->setHasIncrement()
        ;

        $counters = $this->counterService->search($counterSearcher)->getItems();

        foreach ($counters as $counter) {
            if ($counter->getIncrement() === null || $counter->getAccountId() === AccountIdEnum::SNT->value) {
                continue;
            }

            $lastHistory = $this->counterHistoryService->getLastByCounterId($counter->getId());

            if ($lastHistory && $lastHistory->getDate()?->isCurrentMonth()) {
                continue;
            }

            $history = $this->counterHistoryFactory->makeDefault();
            $history
                ->setCounterId($counter->getId())
                ->setValue((int) $lastHistory?->getValue() + (int) $counter->getIncrement())
                ->setIsVerified((bool) $lastHistory?->isVerified())
            ;

            $history = $this->counterHistoryService->save($history);

            $this->historyChangesService->writeToHistory(
                Event::COMMON,
                HistoryType::COUNTER,
                $history->getCounterId(),
                HistoryType::COUNTER_HISTORY,
                $history->getId(),
                text: sprintf('Автоматическое прирощение показаний: на  %sкВт', $counter->getIncrement()),
            );
        }
    }
}
