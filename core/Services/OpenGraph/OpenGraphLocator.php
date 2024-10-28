<?php declare(strict_types=1);

namespace Core\Services\OpenGraph;

use Core\Services\OpenGraph\Factories\OpenGraphFactory;

class OpenGraphLocator
{
    private static OpenGraphFactory $openGraphFactory;

    public static function OpenGraphFactory(): OpenGraphFactory
    {
        if ( ! isset(self::$openGraphFactory)) {
            self::$openGraphFactory = new OpenGraphFactory();
        }

        return self::$openGraphFactory;
    }
}
