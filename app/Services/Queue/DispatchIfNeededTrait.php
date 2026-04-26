<?php declare(strict_types=1);

namespace App\Services\Queue;

use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\LockLocator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;
use Throwable;

trait DispatchIfNeededTrait
{
    protected const int TTL = 60;

    abstract protected static function getLockName(): LockNameEnum;

    abstract protected function getIdentificator(): null|int|string;

    protected static function ttl(): int
    {
        return self::TTL;
    }

    /**
     * Диспатчит задачу только если она не была уже запущена для этого счёта
     */
    public static function dispatchIfNeeded(mixed ...$args): bool
    {
        $lockService = LockLocator::LockService();
        $lockName    = self::getLockName();
        $job         = new self(...$args);

        // Проверяем доступность блокировки
        if ( ! $lockService->isAvailable($lockName, $job->getIdentificator())) {
            return false;
        }

        // 1. Сначала создаём блокировку
        $lockService->lock($lockName, self::ttl(), $job->getIdentificator());

        // 2. Диспатчим джобу
        dispatch($job);

        return true;
    }

    /**
     * Сейчас же диспатчит задачу только если она не была уже запущена для этого счёта
     */
    public static function dispatchSyncIfNeeded(mixed ...$args): bool
    {
        $lockService = LockLocator::LockService();
        $lockName    = self::getLockName();
        $job         = new self(...$args);

        // Проверяем доступность блокировки
        if ( ! $lockService->isAvailable($lockName, $job->getIdentificator())) {
            return false;
        }

        // 1. Сначала создаём блокировку
        $lockService->lock($lockName, self::ttl(), $job->getIdentificator());

        // 2. Диспатчим джобу
        dispatch_sync($job);

        return true;
    }

    public function handle(): void
    {
        try {
            $closure = fn() => $this->callProcess();

            DB::transaction($closure);
        }
        finally {
            $this->releaseLock();
        }
    }

    /**
     * Освобождает блокировку
     */
    private function releaseLock(): void
    {
        try {
            LockLocator::LockService()->release(self::getLockName(), $this->getIdentificator());
        }
        catch (Throwable $e) {
            Log::error('Не вышло освободить блокировку', [
                'lock_name' => self::getLockName()->value,
                'id'        => $this->getIdentificator(),
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Обработчик ошибок (вызывается после всех попыток)
     */
    public function failed(Throwable $exception): void
    {
        $this->releaseLock();
    }

    private function callProcess(): mixed
    {
        $method = new ReflectionMethod($this, 'process');
        $arguments = [];

        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ( ! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new RuntimeException(sprintf(
                    'Job process dependency "%s" in %s must be a resolvable class type.',
                    $parameter->getName(),
                    static::class,
                ));
            }

            $arguments[] = app($type->getName());
        }

        return $this->process(...$arguments);
    }
}
