<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice;

use Core\Domains\Billing\Invoice\Factories\InvoiceFactory;
use Core\Domains\Billing\Invoice\Repositories\InvoiceRepository;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

abstract class InvoiceLocator
{
    private static InvoiceService    $invoiceService;
    private static InvoiceFactory    $invoiceFactory;
    private static InvoiceRepository $invoiceRepository;

    public static function InvoiceService(): InvoiceService
    {
        if ( ! isset(self::$invoiceService)) {
            self::$invoiceService = new InvoiceService(
                self::InvoiceFactory(),
                self::InvoiceRepository(),
                HistoryChangesLocator::HistoryChangesService(),
            );
        }

        return self::$invoiceService;
    }

    public static function InvoiceFactory(): InvoiceFactory
    {
        if ( ! isset(self::$invoiceFactory)) {
            self::$invoiceFactory = new InvoiceFactory();
        }

        return self::$invoiceFactory;
    }

    public static function InvoiceRepository(): InvoiceRepository
    {
        if ( ! isset(self::$invoiceRepository)) {
            self::$invoiceRepository = new InvoiceRepository();
        }

        return self::$invoiceRepository;
    }
}
