<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring;

use Core\Domains\Billing\Acquiring\Factories\AcquiringFactory;
use Core\Domains\Billing\Acquiring\Repositories\AcquiringRepository;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Acquiring\Services\AcquiringWrapper;
use Core\Domains\Billing\Acquiring\Services\ProviderGateway;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

abstract class AcquiringLocator
{
    private static AcquiringService    $acquiringService;
    private static AcquiringFactory    $acquiringFactory;
    private static AcquiringRepository $acquiringRepository;
    private static ProviderGateway     $providerGateway;

    public static function AcquiringWrapper(
        InvoiceDTO $invoiceDTO,
        float      $amount,
        int        $userId,
    ): AcquiringWrapper
    {
        return new AcquiringWrapper(
            $invoiceDTO,
            $amount,
            $userId,
            self::AcquiringService(),
            self::AcquiringFactory(),
            self::ProviderGateway(),
            HistoryChangesLocator::HistoryChangesService(),
        );
    }

    public static function ProviderGateway(): ProviderGateway
    {
        if ( ! isset(self::$providerGateway)) {
            self::$providerGateway = new ProviderGateway();
        }

        return self::$providerGateway;
    }

    public static function AcquiringService(): AcquiringService
    {
        if ( ! isset(self::$acquiringService)) {
            self::$acquiringService = new AcquiringService(
                self::AcquiringFactory(),
                self::AcquiringRepository(),
            );
        }

        return self::$acquiringService;
    }

    public static function AcquiringRepository(): AcquiringRepository
    {
        if ( ! isset(self::$acquiringRepository)) {
            self::$acquiringRepository = new AcquiringRepository();
        }

        return self::$acquiringRepository;
    }

    public static function AcquiringFactory(): AcquiringFactory
    {
        if ( ! isset(self::$acquiringFactory)) {
            self::$acquiringFactory = new AcquiringFactory();
        }

        return self::$acquiringFactory;
    }
}
