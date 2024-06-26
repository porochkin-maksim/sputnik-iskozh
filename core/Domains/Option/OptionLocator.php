<?php declare(strict_types=1);

namespace Core\Domains\Option;

use Core\Domains\Option\Factories\OptionFactory;
use Core\Domains\Option\Factories\TariffFactory;
use Core\Domains\Option\Repositories\OptionRepository;
use Core\Domains\Option\Services\OptionService;

class OptionLocator
{
    private static OptionService    $optionService;
    private static OptionFactory    $optionFactory;
    private static TariffFactory    $tariffFactory;
    private static OptionRepository $optionRepository;

    public static function OptionService(): OptionService
    {
        if ( ! isset(self::$optionService)) {
            self::$optionService = new OptionService(
                self::OptionRepository(),
                self::OptionFactory(),
                self::TariffFactory(),
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

    public static function TariffFactory(): TariffFactory
    {
        if ( ! isset(self::$tariffFactory)) {
            self::$tariffFactory = new TariffFactory();
        }

        return self::$tariffFactory;
    }

    public static function OptionRepository(): OptionRepository
    {
        if ( ! isset(self::$optionRepository)) {
            self::$optionRepository = new OptionRepository();
        }

        return self::$optionRepository;
    }
}
