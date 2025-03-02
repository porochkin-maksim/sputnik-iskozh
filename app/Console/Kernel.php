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
            ->everyFiveMinutes()
            ->name('queue.restart')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/queue/restart.log'));

        $schedule->command(sprintf('queue:work --queue=%s --sleep=3 --tries=3 --stop-when-empty', implode(',', QueueEnum::values())))
            ->everyFifteenSeconds()
            ->name('queue.work-normal')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/queue/work-normal.log'));
    }
}
