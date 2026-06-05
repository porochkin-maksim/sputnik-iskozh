<?php declare(strict_types=1);

namespace App\Console\Commands\Counters;

use App\Locators\CounterLocator;
use Carbon\Carbon;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use env;
use Illuminate\Console\Command;

class GenerateCounterReadings extends Command
{
    protected $signature   = 'counters:generate-readings {--months=12 : Количество месяцев для генерации} {--start-date= : Начальная дата в формате Y-m-d}';
    protected $description = 'Генерация фейковых показаний для существующих счетчиков';

    public function __construct(
        private readonly CounterHistoryFactory $counterHistoryFactory,
        private readonly CounterHistoryService $counterHistoryService,
    )
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (env::isProduction()) {
            $this->error("Приложение запущено в боевом режиме");
            return 1;
        }

        $months    = (int) $this->option('months');
        $startDate = $this->option('start-date')
            ? Carbon::parse($this->option('start-date'))
            : Carbon::now()->subMonths($months);

        $counters = CounterLocator::CounterService()->search(CounterSearcher::make())->getItems();

        if ($counters->isEmpty()) {
            $this->error('Нет активных счетчиков для генерации данных');

            return 1;
        }

        $this->info("Начинаем генерацию показаний для {$counters->count()} счетчиков...");

        $bar = $this->output->createProgressBar($counters->count());
        $bar->start();

        foreach ($counters as $counter) {
            $this->generateReadingsForCounter($counter, $startDate, $months);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Генерация показаний завершена успешно!');

        return 0;
    }

    private function generateReadingsForCounter(CounterEntity $counter, Carbon $startDate, ?int $months = null): void
    {
        // Базовое значение для счетчика (случайное в диапазоне 1000-5000)
        $baseValue    = random_int(1000, 5000);
        $currentValue = $baseValue;
        $date         = $startDate->copy();
        $months       = $months ? : $startDate->diffInMonths(Carbon::now());

        $lastHistory = null;
        // Определяем средний месячный прирост (случайное значение в диапазоне 100-2000)
        $monthlyIncrement = random_int(100, 2000);


        for ($i = 0; $i < $months; $i++) {
            // С вероятностью 5% генерируем аномально низкое потребление
            if (random_int(1, 100) <= 5) {
                $currentValue = (int) $lastHistory?->getValue() + $monthlyIncrement * 0.1; // 10% от предыдущего значения
            }
            else {
                // Нормальное потребление с небольшими отклонениями
                $deviation    = random_int(-10, 10) / 100; // Отклонение ±10%
                $currentValue += $monthlyIncrement * (1 + $deviation);
            }

            $history = $this->counterHistoryFactory->makeDefault()
                ->setCounterId($counter->getId())
                ->setPreviousId($lastHistory?->getId())
                ->setPreviousValue((int) $lastHistory?->getValue())
                ->setValue((int) round($currentValue, 2))
                ->setDate($date)
                ->setIsVerified(true)
            ;

            $history     = $this->counterHistoryService->save($history);
            $lastHistory = $history;

            $date->addMonth();
        }
    }
} 
