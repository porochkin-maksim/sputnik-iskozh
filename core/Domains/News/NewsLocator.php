<?php declare(strict_types=1);

namespace Core\Domains\News;

use Core\Domains\File\FileLocator;
use Core\Domains\News\Factories\NewsFactory;
use Core\Domains\News\Repositories\FileRepository;
use Core\Domains\News\Repositories\NewsRepository;
use Core\Domains\News\Services\FileService;
use Core\Domains\News\Services\NewsService;

class NewsLocator
{
    private static NewsService      $reportService;
    private static NewsFactory    $reportFactory;
    private static NewsRepository $reportRepository;
    private static FileService    $fileService;
    private static FileRepository   $fileRepository;

    public static function NewsService(): NewsService
    {
        if ( ! isset(self::$reportService)) {
            self::$reportService = new NewsService(
                self::NewsFactory(),
                self::NewsRepository(),
            );
        }

        return self::$reportService;
    }

    public static function NewsFactory(): NewsFactory
    {
        if ( ! isset(self::$reportFactory)) {
            self::$reportFactory = new NewsFactory(
                FileLocator::FileFactory(),
            );
        }

        return self::$reportFactory;
    }

    public static function NewsRepository(): NewsRepository
    {
        if ( ! isset(self::$reportRepository)) {
            self::$reportRepository = new NewsRepository();
        }

        return self::$reportRepository;
    }

    public static function FileService(): FileService
    {
        if ( ! isset(self::$fileService)) {
            self::$fileService = new FileService(
                FileLocator::FileService(),
            );
        }

        return self::$fileService;
    }

    public static function FileRepository(): FileRepository
    {
        if ( ! isset(self::$fileRepository)) {
            self::$fileRepository = new FileRepository(
                FileLocator::FileRepository(),
            );
        }

        return self::$fileRepository;
    }
}
