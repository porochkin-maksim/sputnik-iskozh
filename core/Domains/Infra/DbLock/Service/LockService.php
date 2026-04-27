<?php declare(strict_types=1);

namespace Core\Domains\Infra\DbLock\Service;

use App\Models\Infra\Lock;
use Carbon\Carbon;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LockService
{
    /**
     * Проверяет, заблокировано ли имя (активная блокировка существует)
     */
    public function isLocked(LockNameEnum $lockName, string|int|null $postfix = null): bool
    {
        return Lock::where(Lock::NAME, $this->makeName($lockName, $postfix))
            ->where(Lock::EXPIRE_AT, '>', Carbon::now())
            ->exists()
        ;
    }

    /**
     * Проверяет, доступно ли имя для захвата (нет активной блокировки)
     */
    public function isAvailable(LockNameEnum $lockName, string|int|null $postfix = null): bool
    {
        return ! $this->isLocked($lockName, $postfix);
    }

    /**
     * Захватывает блокировку.
     *
     * @throws RuntimeException Если блокировка уже активна
     */
    public function lock(LockNameEnum $lockName, int $minutes = 60, string|int|null $postfix = null): void
    {
        $name = $this->makeName($lockName, $postfix);

        DB::transaction(function () use ($name, $minutes) {
            // Удаляем истекшие блокировки
            Lock::where(Lock::NAME, $name)
                ->where(Lock::EXPIRE_AT, '<=', Carbon::now())
                ->delete()
            ;

            // Проверяем наличие активной блокировки
            $locked = Lock::where(Lock::NAME, $name)
                ->where(Lock::EXPIRE_AT, '>', Carbon::now())
                ->lockForUpdate()
                ->exists()
            ;

            if ($locked) {
                throw new RuntimeException("Блокировка '{$name}' уже активна.");
            }

            // Создаём новую блокировку
            Lock::create([
                Lock::NAME      => $name,
                Lock::EXPIRE_AT => Carbon::now()->addMinutes($minutes),
            ]);
        });
    }

    /**
     * Освобождает блокировку (удаляет все записи с данным именем).
     */
    public function release(LockNameEnum $lockName, string|int|null $postfix = null): void
    {
        Lock::where(Lock::NAME, $this->makeName($lockName, $postfix))->delete();
    }

    /**
     * Продлевает время жизни блокировки
     */
    public function extend(LockNameEnum $lockName, int $additionalMinutes = 60, string|int|null $postfix = null): bool
    {
        $name = $this->makeName($lockName, $postfix);

        return (bool) Lock::where(Lock::NAME, $name)
            ->where(Lock::EXPIRE_AT, '>', Carbon::now())
            ->update([
                Lock::EXPIRE_AT => DB::raw("DATE_ADD(" . Lock::EXPIRE_AT . ", INTERVAL {$additionalMinutes} MINUTE)"),
            ])
        ;
    }

    /**
     * Возвращает оставшееся время блокировки в секундах
     */
    public function getRemainingTime(LockNameEnum $lockName, string|int|null $postfix = null): ?int
    {
        $lock = Lock::where(Lock::NAME, $this->makeName($lockName, $postfix))
            ->where(Lock::EXPIRE_AT, '>', Carbon::now())
            ->first()
        ;

        return $lock ? (int) Carbon::now()->diffInSeconds($lock->expire_at) : null;
    }

    private function makeName(LockNameEnum $lockName, string|int|null $postfix = null): string
    {
        return $postfix !== null
            ? $lockName->value . '-' . $postfix
            : $lockName->value;
    }
}