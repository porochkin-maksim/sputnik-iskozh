<?php declare(strict_types=1);

namespace App\Console\Commands\Counters;

use Carbon\Carbon;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Env;
use Illuminate\Console\Command;

class GenerateCounterReadings extends Command
{
    protected $signature   = 'counters:generate-readings {--months=12 : Количество месяцев для генерации} {--start-date= : Начальная дата в формате Y-m-d}';
    protected $description = 'Генерация фейковых показаний для существующих счетчиков';

    public function handle(): int
    {
        if (Env::isProduction()) {
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

    private function generateReadingsForCounter(CounterDTO $counter, Carbon $startDate, ?int $months = null): void
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

            $history = CounterLocator::CounterHistoryFactory()->makeDefault()
                ->setCounterId($counter->getId())
                ->setPreviousId($lastHistory?->getId())
                ->setPreviousValue((int) $lastHistory?->getValue())
                ->setValue((int) round($currentValue, 2))
                ->setDate($date)
                ->setIsVerified(true)
            ;

            $history     = CounterLocator::CounterHistoryService()->save($history);
            $lastHistory = $history;

            $date->addMonth();
        }
    }
} 