<?php declare(strict_types=1);

namespace Core\Domains\Infra\DbLock\Jobs;

use Closure;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\LockLocator;

class ReleaseLockMiddleware
{
    private LockNameEnum $lockName;

    public function __construct(LockNameEnum $lockName)
    {
        $this->lockName = $lockName;
    }

    public function handle($job, Closure $next): void
    {
        try {
            $next($job);
        }
        finally {
            LockLocator::LockService()->release($this->lockName);
        }
    }
}
