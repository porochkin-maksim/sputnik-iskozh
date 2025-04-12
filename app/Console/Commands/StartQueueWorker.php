<?php declare(strict_types=1);

namespace App\Console\Commands;

use Core\Queue\QueueEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StartQueueWorker extends Command
{
    protected $signature = 'queue:worker:start {--workers=1 : Number of workers per queue}';
    protected $description = 'Start multiple queue workers for each queue';

    public function handle(): void
    {
        $workersPerQueue = (int)$this->option('workers');
        $this->info("Starting {$workersPerQueue} workers per queue...");

        foreach (QueueEnum::values() as $queue) {
            $this->startWorkersForQueue($queue, $workersPerQueue);
        }

        $this->info('All workers started successfully');
    }

    private function startWorkersForQueue(string $queue, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $this->startWorker($queue, $i);
        }
    }

    private function startWorker(string $queue, int $workerNumber): void
    {
        $command = "php artisan queue:work database --queue={$queue} --stop-when-empty --sleep=3 --tries=3 --verbose";
        $logFile = storage_path("logs/queue-{$queue}-worker-{$workerNumber}.log");

        // Проверяем, не запущен ли уже такой воркер
        $processName = "queue:work database --queue={$queue}";
        $existingProcesses = shell_exec("pgrep -f '{$processName}'");

        if (!empty($existingProcesses)) {
            $this->warn("Worker for queue '{$queue}' (worker {$workerNumber}) is already running");
            return;
        }

        // Запускаем воркер в фоновом режиме
        $command .= " > {$logFile} 2>&1 &";

        try {
            exec($command);
            $this->info("Started worker for queue '{$queue}' (worker {$workerNumber})");
        } catch (\Exception $e) {
            $this->error("Failed to start worker for queue '{$queue}' (worker {$workerNumber}): " . $e->getMessage());
        }
    }
} 