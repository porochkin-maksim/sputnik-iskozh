<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges;

use Core\Domains\Infra\Comparator\ComparatorLocator;
use Core\Domains\Infra\HistoryChanges\Decorator\HistoryChangesDecorator;
use Core\Domains\Infra\HistoryChanges\Factories\HistoryChangesFactory;
use Core\Domains\Infra\HistoryChanges\Models\HistoryChangesDTO;
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

    private static function HistoryChangesFactory(): HistoryChangesFactory
    {
        if ( ! isset(self::$historyChangesFactory)) {
            self::$historyChangesFactory = new HistoryChangesFactory(
                UserLocator::UserFactory()
            );
        }

        return self::$historyChangesFactory;
    }

    private static function HistoryChangesRepository(): HistoryChangesRepository
    {
        if ( ! isset(self::$historyChangesRepository)) {
            self::$historyChangesRepository = new HistoryChangesRepository();
        }

        return self::$historyChangesRepository;
    }

    public static function HistoryChangesDecorator(HistoryChangesDTO $historyChanges): HistoryChangesDecorator
    {
        return new HistoryChangesDecorator($historyChanges);
    }
}
