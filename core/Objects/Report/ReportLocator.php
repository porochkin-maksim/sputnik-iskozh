<?php declare(strict_types=1);

namespace Core\Objects\Report;

use Core\Objects\File\FileLocator;
use Core\Objects\Report\Factories\ReportFactory;
use Core\Objects\Report\Repositories\FileRepository;
use Core\Objects\Report\Repositories\ReportRepository;
use Core\Objects\Report\Services\FileService;
use Core\Objects\Report\Services\ReportService;

class ReportLocator
{
    private static ReportService    $reportService;
    private static ReportFactory    $reportFactory;
    private static ReportRepository $reportRepository;
    private static FileService      $fileService;
    private static FileRepository   $fileRepository;

    public static function ReportService(): ReportService
    {
        if ( ! isset(self::$reportService)) {
            self::$reportService = new ReportService(
                self::ReportFactory(),
                self::ReportRepository(),
            );
        }

        return self::$reportService;
    }

    public static function ReportFactory(): ReportFactory
    {
        if ( ! isset(self::$reportFactory)) {
            self::$reportFactory = new ReportFactory(
                FileLocator::FileFactory(),
            );
        }

        return self::$reportFactory;
    }

    public static function ReportRepository(): ReportRepository
    {
        if ( ! isset(self::$reportRepository)) {
            self::$reportRepository = new ReportRepository();
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
