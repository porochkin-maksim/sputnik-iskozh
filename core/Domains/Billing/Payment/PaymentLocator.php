<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use Core\Domains\Billing\Payment\Factories\PaymentFactory;
use Core\Domains\Billing\Payment\Repositories\PaymentRepository;
use Core\Domains\Billing\Payment\Services\FileService;
use Core\Domains\Billing\Payment\Services\PaymentService;
use Core\Domains\File\FileLocator;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

abstract class PaymentLocator
{
    private static PaymentService    $paymentService;
    private static PaymentFactory    $paymentFactory;
    private static PaymentRepository $paymentRepository;
    private static FileService       $fileService;

    public static function PaymentService(): PaymentService
    {
        if ( ! isset(self::$paymentService)) {
            self::$paymentService = new PaymentService(
                self::PaymentFactory(),
                self::PaymentRepository(),
                HistoryChangesLocator::HistoryChangesService(),
            );
        }

        return self::$paymentService;
    }

    public static function PaymentFactory(): PaymentFactory
    {
        if ( ! isset(self::$paymentFactory)) {
            self::$paymentFactory = new PaymentFactory();
        }

        return self::$paymentFactory;
    }

    public static function PaymentRepository(): PaymentRepository
    {
        if ( ! isset(self::$paymentRepository)) {
            self::$paymentRepository = new PaymentRepository();
        }

        return self::$paymentRepository;
    }

    public static function FileService(): FileService
    {
        if ( ! isset(self::$fileService)) {
            self::$fileService = new FileService(
                FileLocator::FileService(),
            );
        }

        return self::$fileService;
    }
}
