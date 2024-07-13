<?php declare(strict_types=1);

namespace Core\Domains\File;

use Core\Domains\File\Factories\FolderFactory;
use Core\Domains\File\Repositories\FolderRepository;
use Core\Domains\File\Services\FolderService;

abstract class FolderLocator
{
    private static FolderService    $folderService;
    private static FolderFactory    $folderFactory;
    private static FolderRepository $folderRepository;

    public static function FolderService(): FolderService
    {
        if ( ! isset(self::$folderService)) {
            self::$folderService = new FolderService(
                self::FolderRepository(),
                self::FolderFactory(),
                FileLocator::FileService(),
            );
        }

        return self::$folderService;
    }

    public static function FolderFactory(): FolderFactory
    {
        if ( ! isset(self::$folderFactory)) {
            self::$folderFactory = new FolderFactory();
        }

        return self::$folderFactory;
    }

    public static function FolderRepository(): FolderRepository
    {
        if ( ! isset(self::$folderRepository)) {
            self::$folderRepository = new FolderRepository();
        }

        return self::$folderRepository;
    }
}
