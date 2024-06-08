<?php declare(strict_types=1);

namespace Core\Objects\News;

use Core\Objects\File\FileLocator;
use Core\Objects\News\Factories\NewsFactory;
use Core\Objects\News\Repositories\FileRepository;
use Core\Objects\News\Repositories\NewsRepository;
use Core\Objects\News\Services\FileService;
use Core\Objects\News\Services\NewsService;

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
