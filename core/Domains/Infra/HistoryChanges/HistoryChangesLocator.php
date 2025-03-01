<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges;

use Core\Domains\Infra\Comparator\ComparatorLocator;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\Infra\HistoryChanges\Decorator\LogDataDecorator;
use Core\Domains\Infra\HistoryChanges\Factories\HistoryChangesFactory;
use Core\Domains\Infra\HistoryChanges\Models\LogData;
use Core\Domains\Infra\HistoryChanges\Repositories\HistoryChangesRepository;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Domains\User\UserLocator;

abstract class HistoryChangesLocator
{
    private static HistoryChangesService    $historyChangesService;
    private static HistoryChangesFactory    $historyChangesFactory;
    private static HistoryChangesRepository $historyChangesRepository;

    public static function HistoryChangesService(): HistoryChangesService
    {
        if ( ! isset(self::$historyChangesService)) {
            self::$historyChangesService = new HistoryChangesService(
                self::HistoryChangesFactory(),
                self::HistoryChangesRepository(),
                ComparatorLocator::Comparator(),
            );
        }

        return self::$historyChangesService;
    }

    public static function HistoryChangesFactory(): HistoryChangesFactory
    {
        if ( ! isset(self::$historyChangesFactory)) {
            self::$historyChangesFactory = new HistoryChangesFactory(
                UserLocator::UserFactory()
            );
        }

        return self::$historyChangesFactory;
    }

    public static function HistoryChangesRepository(): HistoryChangesRepository
    {
        if ( ! isset(self::$historyChangesRepository)) {
            self::$historyChangesRepository = new HistoryChangesRepository();
        }

        return self::$historyChangesRepository;
    }

    public static function LogDataDecorator(LogData $logData): LogDataDecorator
    {
        return new LogDataDecorator($logData);
    }
}
