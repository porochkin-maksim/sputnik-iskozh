<?php declare(strict_types=1);

namespace Core\Queue;

use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\LockLocator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

trait DispatchIfNeededTrait
{
    abstract protected static function getLockName(): LockNameEnum;

    abstract protected function process();

    abstract protected function getIdentificator(): null|int|string;

    protected static function ttl(): int
    {
        return 60;
    }

    /**
     * Диспатчит задачу только если она не была уже запущена для этого счёта
     */
    public static function dispatchIfNeeded(null|int|string $id, bool $sync = false): bool
    {
        $lockService = LockLocator::LockService();
        $lockName    = self::getLockName();

        // Проверяем доступность блокировки
        if ( ! $lockService->isAvailable($lockName, $id)) {
            return false;
        }

        // 1. Сначала создаём блокировку
        $lockService->lock($lockName, self::ttl(), $id);

        // 2. Диспатчим джобу
        if ($sync) {
            dispatch_sync(new self($id));
        }
        else {
            dispatch(new self($id));
        }

        return true;
    }


    public function handle(): void
    {
        try {
            $closure = fn() => $this->process();

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
}