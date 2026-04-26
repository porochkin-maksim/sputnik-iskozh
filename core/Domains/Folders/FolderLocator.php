<?php declare(strict_types=1);

namespace Core\Domains\Folders;

use Core\Domains\Files\Factories\FolderFactory;
use Core\Domains\Files\FileLocator;
use Core\Domains\Folders\Repositories\FolderRepository;
use Core\Domains\Folders\Services\FolderService;

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
