<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData;

use Core\Domains\Infra\ExData\Factories\ExDataFactory;
use Core\Domains\Infra\ExData\Repositories\ExDataRepository;
use Core\Domains\Infra\ExData\Services\ExDataService;

abstract class ExDataLocator
{
    private static ?ExDataFactory    $ExDataFactory    = null;
    private static ?ExDataRepository $ExDataRepository = null;
    private static ?ExDataService    $ExDataService    = null;

    public static function ExDataFactory(): ExDataFactory
    {
        if (self::$ExDataFactory === null) {
            self::$ExDataFactory = new ExDataFactory();
        }

        return self::$ExDataFactory;
    }

    public static function ExDataRepository(): ExDataRepository
    {
        if (self::$ExDataRepository === null) {
            self::$ExDataRepository = new ExDataRepository();
        }

        return self::$ExDataRepository;
    }

    public static function ExDataService(): ExDataService
    {
        if (self::$ExDataService === null) {
            self::$ExDataService = new ExDataService(
                self::ExDataFactory(),
                self::ExDataRepository(),
            );
        }

        return self::$ExDataService;
    }
}