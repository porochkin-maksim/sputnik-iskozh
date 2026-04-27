<?php declare(strict_types=1);

namespace Core\Domains\News;

use Core\Domains\Files\FileLocator;
use Core\Domains\News\Factories\NewsFactory;
use Core\Domains\News\Factories\UrlFactory;
use Core\Domains\News\Repositories\FileRepository;
use Core\Domains\News\Repositories\NewsRepository;
use Core\Domains\News\Services\FileService;
use Core\Domains\News\Services\NewsService;

class NewsLocator
{
    private static NewsService    $newsService;
    private static NewsFactory    $newsFactory;
    private static NewsRepository $newsRepository;
    private static FileService    $fileService;
    private static FileRepository $fileRepository;
    private static UrlFactory     $urlFactory;

    public static function NewsService(): NewsService
    {
        if ( ! isset(self::$newsService)) {
            self::$newsService = new NewsService(
                self::NewsFactory(),
                self::NewsRepository(),
            );
        }

        return self::$newsService;
    }

    public static function NewsFactory(): NewsFactory
    {
        if ( ! isset(self::$newsFactory)) {
            self::$newsFactory = new NewsFactory(
                FileLocator::FileFactory(),
            );
        }

        return self::$newsFactory;
    }

    public static function NewsRepository(): NewsRepository
    {
        if ( ! isset(self::$newsRepository)) {
            self::$newsRepository = new NewsRepository();
        }

        return self::$newsRepository;
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

    public static function UrlFactory(): UrlFactory
    {
        if ( ! isset(self::$urlFactory)) {
            self::$urlFactory = new UrlFactory();
        }

        return self::$urlFactory;
    }
}
