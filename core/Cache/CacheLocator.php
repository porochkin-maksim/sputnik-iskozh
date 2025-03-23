<?php declare(strict_types=1);

namespace Core\Cache;

class CacheLocator
{
    private static LocalCache $localCache;

    public static function LocalCache(): LocalCache
    {
        if ( ! isset(self::$localCache)) {
            self::$localCache = new LocalCache();
        }

        return self::$localCache;
    }
}
