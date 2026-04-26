<?php declare(strict_types=1);

namespace App\Locators;

use Core\Domains\Counter\CounterFactory;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;

class CounterLocator
{
    private static CounterService $counterService;
    private static CounterFactory $counterFactory;
    private static FileService $fileService;

    public static function CounterService(): CounterService
    {
        if ( ! isset(self::$counterService)) {
            self::$counterService = app(CounterService::class);
        }

        return self::$counterService;
    }

    public static function CounterFactory(): CounterFactory
    {
        if ( ! isset(self::$counterFactory)) {
            self::$counterFactory = app(CounterFactory::class);
        }

        return self::$counterFactory;
    }

    public static function FileService(): FileService
    {
        if ( ! isset(self::$fileService)) {
            self::$fileService = app(FileService::class);
        }

        return self::$fileService;
    }
}
