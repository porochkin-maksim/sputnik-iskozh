<?php declare(strict_types=1);

namespace App\Locators;

use App\Locators\News\UrlFactory;

class NewsLocator
{
    private static UrlFactory $urlFactory;

    public static function UrlFactory(): UrlFactory
    {
        if ( ! isset(self::$urlFactory)) {
            self::$urlFactory = new UrlFactory();
        }

        return self::$urlFactory;
    }
}
