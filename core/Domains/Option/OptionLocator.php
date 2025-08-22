<?php declare(strict_types=1);

namespace Core\Domains\Option;

use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Option\Factories\ComparatorFactory;
use Core\Domains\Option\Factories\OptionFactory;
use Core\Domains\Option\Repositories\OptionRepository;
use Core\Domains\Option\Services\OptionService;

class OptionLocator
{
    private static OptionService     $optionService;
    private static OptionFactory     $optionFactory;
    private static OptionRepository  $optionRepository;
    private static ComparatorFactory $comparatorFactory;

    public static function OptionService(): OptionService
    {
        if ( ! isset(self::$optionService)) {
            self::$optionService = new OptionService(
                self::OptionFactory(),
                self::OptionRepository(),
                HistoryChangesLocator::HistoryChangesService(),
                self::ComparatorFactory(),
            );
        }

        return self::$optionService;
    }

    public static function OptionFactory(): OptionFactory
    {
        if ( ! isset(self::$optionFactory)) {
            self::$optionFactory = new OptionFactory();
        }

        return self::$optionFactory;
    }

    public static function ComparatorFactory(): ComparatorFactory
    {
        if ( ! isset(self::$comparatorFactory)) {
            self::$comparatorFactory = new ComparatorFactory();
        }

        return self::$comparatorFactory;
    }

    public static function OptionRepository(): OptionRepository
    {
        if ( ! isset(self::$optionRepository)) {
            self::$optionRepository = new OptionRepository();
        }

        return self::$optionRepository;
    }
}
