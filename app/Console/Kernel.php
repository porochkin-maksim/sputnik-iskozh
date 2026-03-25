<?php

namespace App\Console;

use Core\Queue\QueueEnum;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('cache:clear-all')->dailyAt('03:00');
        $schedule->command('schedule:clear-cache')->dailyAt('03:00');

        $this->scheduleQueues($schedule);
        $this->scheduleLocksCleanup($schedule);
        $this->scheduleResetAutoIncrement($schedule);
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
            ->withoutOverlapping()
        ;

        $schedule->command(sprintf('queue:work --queue=%s --stop-when-empty --sleep=3 --tries=3 --verbose', implode(',', QueueEnum::values())))
            ->everyMinute()
            ->name('queue.work-normal')
            ->withoutOverlapping()
        ;

        $schedule->command('storage:logs:clear-unused-files')
            ->dailyAt('03:00')
            ->name('storage.logs.clear-unused-files')
        ;

        $schedule->command('db:clear-old-data')
            ->monthly()
            ->name('db.clear-old-data')
        ;
    }

    /**
     * Расписание очистки блокировок
     */
    private function scheduleLocksCleanup(Schedule $schedule): void
    {
        // Очистка истекших блокировок каждый час
        $schedule->command('db:clear-locks --expired-only --force')
            ->hourly()
            ->name('locks.clear-expired')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/locks-cleanup.log'))
        ;

        // Дополнительно: раз в сутки в 03:30 чистим старые активные блокировки (старше 24 часов)
        $schedule->command('db:clear-locks --older-than=1440 --force')
            ->dailyAt('03:30')
            ->name('locks.clear-stale')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/locks-cleanup.log'))
        ;
    }

    /**
     * Расписание сброса AUTO_INCREMENT для всех таблиц
     */
    private function scheduleResetAutoIncrement(Schedule $schedule): void
    {
        $schedule->command('db:reset-auto-increment --force')
            ->dailyAt('04:00')
            ->name('db.reset-auto-increment')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/auto-increment-reset.log'))
        ;
    }
}