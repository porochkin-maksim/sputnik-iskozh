<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject;

use Core\Domains\Billing\ClaimToObject\Services\ClaimToObjectService;

abstract class ClaimToObjectLocator
{
    private static ?ClaimToObjectService $claimToObjectService;

    public static function ClaimToObjectService(): ClaimToObjectService
    {
        if ( ! isset(self::$claimToObjectService)) {
            self::$claimToObjectService = new ClaimToObjectService();
        }

        return self::$claimToObjectService;
    }
}