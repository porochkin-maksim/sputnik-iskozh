<?php declare(strict_types=1);

namespace App\Jobs\Infra\DbLock;

use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\LockLocator;
use Core\Domains\Infra\DbLock\Service\LockService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class LockedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string       $jobClass,
        private readonly array        $jobArgs,
        private readonly LockNameEnum $lockName,
    )
    {
    }

    public function handle(LockService $lockService): void
    {
        try {
            // Создаём экземпляр джобы с переданными аргументами
            $job = new $this->jobClass(...$this->jobArgs);

            // Диспатчим джобу
            dispatch_sync($job);
        }
        finally {
            $lockService->release($this->lockName);
        }
    }

    public function failed(Throwable $exception): void
    {
        if (method_exists($this->jobClass, 'failed')) {
            // Создаём экземпляр для вызова failed (если нужно передать контекст)
            $job = new $this->jobClass(...$this->jobArgs);
            if (method_exists($job, 'failed')) {
                $job->failed($exception);
            }
        }

        LockLocator::LockService()->release($this->lockName);
    }
}