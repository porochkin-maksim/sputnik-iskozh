<?php declare(strict_types=1);

namespace App\Services\Images;

use App\Services\Images\Factories\StaticFileFactory;
use App\Services\Images\Services\StaticFileService;

abstract class StaticFileLocator
{
    private static StaticFileService $staticFilesService;
    private static StaticFileFactory $staticFileFactory;

    public static function StaticFileService(): StaticFileService
    {
        if ( ! isset(self::$staticFilesService)) {
            self::$staticFilesService = new StaticFileService(
                self::StaticFileFactory(),
            );
        }

        return self::$staticFilesService;
    }

    private static function StaticFileFactory(): StaticFileFactory
    {
        if ( ! isset(self::$staticFileFactory)) {
            self::$staticFileFactory = new StaticFileFactory();
        }

        return self::$staticFileFactory;
    }
}
