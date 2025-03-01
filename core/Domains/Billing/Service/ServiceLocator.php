<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service;

use Core\Domains\Billing\Service\Factories\ServiceFactory;
use Core\Domains\Billing\Service\Repositories\ServiceRepository;
use Core\Domains\Billing\Service\Services\ServiceService;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

abstract class ServiceLocator
{
    private static ServiceService    $serviceService;
    private static ServiceFactory    $serviceFactory;
    private static ServiceRepository $serviceRepository;

    public static function ServiceService(): ServiceService
    {
        if ( ! isset(self::$serviceService)) {
            self::$serviceService = new ServiceService(
                self::ServiceFactory(),
                self::ServiceRepository(),
                HistoryChangesLocator::HistoryChangesService(),
            );
        }

        return self::$serviceService;
    }

    public static function ServiceFactory(): ServiceFactory
    {
        if ( ! isset(self::$serviceFactory)) {
            self::$serviceFactory = new ServiceFactory();
        }

        return self::$serviceFactory;
    }

    public static function ServiceRepository(): ServiceRepository
    {
        if ( ! isset(self::$serviceRepository)) {
            self::$serviceRepository = new ServiceRepository();
        }

        return self::$serviceRepository;
    }
}
