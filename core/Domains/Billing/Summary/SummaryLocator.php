<?php declare(strict_types=1);

namespace Core\Domains\Billing\Summary;

use Core\Domains\Billing\Summary\Repositories\SummaryRepository;
use Core\Domains\Billing\Summary\Services\SummaryService;

abstract class SummaryLocator
{
    private static SummaryService    $summaryService;
    private static SummaryRepository $summaryRepository;

    public static function SummaryService(): SummaryService
    {
        if ( ! isset(self::$summaryService)) {
            self::$summaryService = new SummaryService(
                self::SummaryRepository(),
            );
        }

        return self::$summaryService;
    }

    public static function SummaryRepository(): SummaryRepository
    {
        if ( ! isset(self::$summaryRepository)) {
            self::$summaryRepository = new SummaryRepository();
        }

        return self::$summaryRepository;
    }
}
