<?php declare(strict_types=1);

namespace Core\Domains\File;

use Core\Domains\File\Factories\FileFactory;
use Core\Domains\File\Repositories\FileRepository;
use Core\Domains\File\Services\FileService;

abstract class FileLocator
{
    private static FileService    $fileService;
    private static FileFactory    $fileFactory;
    private static FileRepository $fileRepository;

    public static function FileService(): FileService
    {
        if ( ! isset(self::$fileService)) {
            self::$fileService = new FileService(
                self::FileRepository(),
                self::FileFactory(),
            );
        }

        return self::$fileService;
    }

    public static function FileFactory(): FileFactory
    {
        if ( ! isset(self::$fileFactory)) {
            self::$fileFactory = new FileFactory();
        }

        return self::$fileFactory;
    }

    public static function FileRepository(): FileRepository
    {
        if ( ! isset(self::$fileRepository)) {
            self::$fileRepository = new FileRepository();
        }

        return self::$fileRepository;
    }
}
