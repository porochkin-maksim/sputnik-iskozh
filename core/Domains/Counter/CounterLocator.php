<?php declare(strict_types=1);

namespace Core\Domains\Counter;

use Core\Domains\Counter\Factories\CounterFactory;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Repositories\CounterHistoryRepository;
use Core\Domains\Counter\Repositories\CounterRepository;
use Core\Domains\Counter\Services\CounterHistoryService;
use Core\Domains\Counter\Services\CounterService;
use Core\Domains\Counter\Services\FileService;
use Core\Domains\File\FileLocator;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

class CounterLocator
{
    private static CounterService           $counterService;
    private static CounterFactory           $counterFactory;
    private static CounterRepository        $counterRepository;
    private static CounterHistoryService    $counterHistoryService;
    private static CounterHistoryFactory    $counterHistoryFactory;
    private static CounterHistoryRepository $counterHistoryRepository;
    private static FileService              $fileService;

    public static function CounterService(): CounterService
    {
        if ( ! isset(self::$counterService)) {
            self::$counterService = new CounterService(
                self::CounterFactory(),
                self::CounterRepository(),
                HistoryChangesLocator::HistoryChangesService(),
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

    public static function CounterHistoryService(): CounterHistoryService
    {
        if ( ! isset(self::$counterHistoryService)) {
            self::$counterHistoryService = new CounterHistoryService(
                self::CounterHistoryFactory(),
                self::CounterHistoryRepository(),
                HistoryChangesLocator::HistoryChangesService(),
            );
        }

        return self::$counterHistoryService;
    }

    public static function CounterHistoryFactory(): CounterHistoryFactory
    {
        if ( ! isset(self::$counterHistoryFactory)) {
            self::$counterHistoryFactory = new CounterHistoryFactory();
        }

        return self::$counterHistoryFactory;
    }

    public static function CounterHistoryRepository(): CounterHistoryRepository
    {
        if ( ! isset(self::$counterHistoryRepository)) {
            self::$counterHistoryRepository = new CounterHistoryRepository();
        }

        return self::$counterHistoryRepository;
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
