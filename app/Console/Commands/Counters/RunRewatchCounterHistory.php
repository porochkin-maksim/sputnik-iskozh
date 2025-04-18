<?php declare(strict_types=1);

namespace App\Console\Commands\Counters;

use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Jobs\RewatchCounterHistoryChainJob;
use Core\Domains\Counter\Models\CounterSearcher;
use Illuminate\Console\Command;

class RunRewatchCounterHistory extends Command
{
    protected $signature   = 'counters:rewatch';
    protected $description = 'Запускает переобход счётчиков и их цепочек';

    public function handle(): int
    {
        $counters = CounterLocator::CounterService()->search(CounterSearcher::make())->getItems();

        if ($counters->isEmpty()) {
            $this->error('Нет активных счетчиков для переобхода');

            return 1;
        }

        foreach ($counters as $counter) {
            $this->info("Переобход счетчика {$counter->getId()}");
            dispatch_sync(new RewatchCounterHistoryChainJob($counter->getId()));
        }

        return 0;
    }
} 