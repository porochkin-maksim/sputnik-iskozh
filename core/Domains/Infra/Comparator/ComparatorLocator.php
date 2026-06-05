<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator;

use Core\Domains\Infra\Comparator\Factories\ChangesFactory;
use Core\Domains\Infra\Comparator\Services\Comparator;

class ComparatorLocator
{
    private static Comparator     $comparator;
    private static ChangesFactory $historyChangesFactory;

    public static function Comparator(): Comparator
    {
        if ( ! isset(self::$comparator)) {
            self::$comparator = new Comparator(
                self::HistoryChangesFactory(),
            );
        }

        return self::$comparator;
    }

    private static function HistoryChangesFactory(): ChangesFactory
    {
        if ( ! isset(self::$historyChangesFactory)) {
            self::$historyChangesFactory = new ChangesFactory();
        }

        return self::$historyChangesFactory;
    }
}
