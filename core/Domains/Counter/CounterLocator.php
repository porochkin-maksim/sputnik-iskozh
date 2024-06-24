<?php declare(strict_types=1);

namespace Core\Domains\Counter;

use Core\Domains\Counter\Factories\CounterFactory;
use Core\Domains\Counter\Repositories\CounterRepository;
use Core\Domains\Counter\Services\CounterService;

class CounterLocator
{
    private static CounterService    $counterService;
    private static CounterFactory    $counterFactory;
    private static CounterRepository $counterRepository;

    public static function CounterService(): CounterService
    {
        if ( ! isset(self::$counterService)) {
            self::$counterService = new CounterService(
                self::CounterFactory(),
                self::CounterRepository(),
            );
        }

        return self::$counterService;
    }

    public static function CounterFactory(): CounterFactory
    {
        if ( ! isset(self::$counterFactory)) {
            self::$counterFactory = new CounterFactory();
        }

        return self::$counterFactory;
    }

    public static function CounterRepository(): CounterRepository
    {
        if ( ! isset(self::$counterRepository)) {
            self::$counterRepository = new CounterRepository();
        }

        return self::$counterRepository;
    }
}
