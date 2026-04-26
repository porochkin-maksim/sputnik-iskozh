<?php declare(strict_types=1);

namespace App\Locators;

use App\Helpers\FileStorage;
use App\Helpers\StringGenerator;
use App\Repositories\Files\FileEloquentMapper;
use App\Repositories\Files\FileEloquentRepository;
use Core\Domains\Files\FileService;

abstract class FileLocator
{
    private static FileService            $fileService;
    private static FileEloquentMapper     $fileFactory;
    private static FileEloquentRepository $fileRepository;

    public static function FileService(): FileService
    {
        if ( ! isset(self::$fileService)) {
            self::$fileService = new FileService(
                self::FileRepository(),
                new FileStorage(),
                new StringGenerator(),
            );
        }

        return self::$fileService;
    }

    public static function FileFactory(): FileEloquentMapper
    {
        if ( ! isset(self::$fileFactory)) {
            self::$fileFactory = new FileEloquentMapper();
        }

        return self::$fileFactory;
    }

    public static function FileRepository(): FileEloquentRepository
    {
        if ( ! isset(self::$fileRepository)) {
            self::$fileRepository = new FileEloquentRepository(
                self::FileFactory(),
            );
        }

        return self::$fileRepository;
    }
}
