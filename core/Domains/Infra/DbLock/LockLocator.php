<?php declare(strict_types=1);

namespace Core\Domains\Infra\DbLock;

use Core\Domains\Infra\DbLock\Service\LockService;

abstract class LockLocator
{
    private static LockService $lockService;

    public static function LockService(): LockService
    {
        if ( ! isset(self::$lockService)) {
            self::$lockService = new LockService();
        }

        return self::$lockService;
    }
}