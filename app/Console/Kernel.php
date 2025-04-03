<?php

namespace App\Console;

use Core\Queue\QueueEnum;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('schedule:clear-cache')->dailyAt('03:00');

        $this->scheduleQueues($schedule);
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    private function scheduleQueues(Schedule $schedule): void
    {
        $schedule->command('queue:restart')
            ->hourly()
            ->name('queue.restart')
            ->withoutOverlapping();

        $schedule->command(sprintf('queue:work --queue=%s --sleep=3 --tries=3 --verbose', implode(',', QueueEnum::values())))
            ->everySecond()
            ->name('queue.work-normal')
            ->withoutOverlapping();
    }
}
