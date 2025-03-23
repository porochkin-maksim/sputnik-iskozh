<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction;

use Core\Domains\Billing\Transaction\Factories\TransactionFactory;
use Core\Domains\Billing\Transaction\Repositories\TransactionRepository;
use Core\Domains\Billing\Transaction\Services\TransactionService;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

abstract class TransactionLocator
{
    private static TransactionService    $transactionService;
    private static TransactionFactory    $transactionFactory;
    private static TransactionRepository $transactionRepository;

    public static function TransactionService(): TransactionService
    {
        if ( ! isset(self::$transactionService)) {
            self::$transactionService = new TransactionService(
                self::TransactionFactory(),
                self::TransactionRepository(),
                HistoryChangesLocator::HistoryChangesService(),
            );
        }

        return self::$transactionService;
    }

    public static function TransactionFactory(): TransactionFactory
    {
        if ( ! isset(self::$transactionFactory)) {
            self::$transactionFactory = new TransactionFactory();
        }

        return self::$transactionFactory;
    }

    public static function TransactionRepository(): TransactionRepository
    {
        if ( ! isset(self::$transactionRepository)) {
            self::$transactionRepository = new TransactionRepository();
        }

        return self::$transactionRepository;
    }
}
