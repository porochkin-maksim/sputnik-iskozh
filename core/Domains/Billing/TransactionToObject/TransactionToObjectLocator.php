<?php declare(strict_types=1);

namespace Core\Domains\Billing\TransactionToObject;

use Core\Domains\Billing\TransactionToObject\Services\TransactionToObjectService;

abstract class TransactionToObjectLocator
{
    private static ?TransactionToObjectService $transactionToObjectService;

    public static function TransactionToObjectService(): TransactionToObjectService
    {
        if ( ! isset(self::$transactionToObjectService)) {
            self::$transactionToObjectService = new TransactionToObjectService();
        }

        return self::$transactionToObjectService;
    }
}