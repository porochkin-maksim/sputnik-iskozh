<?php declare(strict_types=1);

namespace App\Services\Queue;

use Core\Domains\Infra\DbLock\Enum\LockNameEnum;

/**
 * Интерфейс для джоб, которые используют блокировки и транзакции.
 * Должен быть реализован в классах вместе с трейтом DispatchIfNeededTrait.
 */
interface LockableJobInterface
{
    /**
     * Возвращает имя блокировки для данного типа джоб.
     */
    public static function getLockName(): LockNameEnum;

    /**
     * Возвращает уникальный идентификатор конкретного экземпляра задачи
     * (например, ID счёта или комбинацию полей).
     *
     * @return int|string|null
     */
    public function getIdentificator(): null|int|string;

    /**
     * Диспатчит задачу только если она не была уже запущена для этого счёта
     */
    public static function dispatchIfNeeded(mixed ...$args): bool;

    /**
     * Сейчас же диспатчит задачу только если она не была уже запущена для этого счёта
     */
    public static function dispatchSyncIfNeeded(mixed ...$args): bool;

}
