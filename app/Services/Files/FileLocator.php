<?php declare(strict_types=1);

namespace App\Services\Files;

use App\Services\Files\Factories\MetaDataFactory;
use App\Services\Files\Services\TmpFileService;

abstract class FileLocator
{
    private static TmpFileService  $TmpFileService;
    private static MetaDataFactory $MetaDataFactory;

    public static function TmpFileService(): TmpFileService
    {
        if ( ! isset(self::$TmpFileService)) {
            self::$TmpFileService = new TmpFileService(
                self::MetaDataFactory(),
            );
        }

        return self::$TmpFileService;
    }

    private static function MetaDataFactory(): MetaDataFactory
    {
        if ( ! isset(self::$MetaDataFactory)) {
            self::$MetaDataFactory = new MetaDataFactory();
        }

        return self::$MetaDataFactory;

    }
}
