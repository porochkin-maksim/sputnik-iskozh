<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period;

use Core\Domains\Billing\Period\Factories\PeriodFactory;
use Core\Domains\Billing\Period\Repositories\PeriodRepository;
use Core\Domains\Billing\Period\Services\PeriodService;

abstract class PeriodLocator
{
    private static PeriodService    $periodService;
    private static PeriodFactory    $periodFactory;
    private static PeriodRepository $periodRepository;

    public static function PeriodService(): PeriodService
    {
        if ( ! isset(self::$periodService)) {
            self::$periodService = new PeriodService(
                self::PeriodRepository(),
            );
        }

        return self::$periodService;
    }

    public static function PeriodFactory(): PeriodFactory
    {
        if ( ! isset(self::$periodFactory)) {
            self::$periodFactory = new PeriodFactory();
        }

        return self::$periodFactory;
    }

    public static function PeriodRepository(): PeriodRepository
    {
        if ( ! isset(self::$periodRepository)) {
            self::$periodRepository = new PeriodRepository(
                self::PeriodFactory(),
            );
        }

        return self::$periodRepository;
    }
}
