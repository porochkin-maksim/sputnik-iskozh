<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk;

use Core\Domains\File\FileLocator;
use Core\Domains\HelpDesk\Factories\TicketCategoryFactory;
use Core\Domains\HelpDesk\Factories\TicketCommentFactory;
use Core\Domains\HelpDesk\Factories\TicketFactory;
use Core\Domains\HelpDesk\Factories\TicketServiceFactory;
use Core\Domains\HelpDesk\Repositories\TicketCategoryRepository;
use Core\Domains\HelpDesk\Repositories\TicketCommentRepository;
use Core\Domains\HelpDesk\Repositories\TicketRepository;
use Core\Domains\HelpDesk\Repositories\TicketServiceRepository;
use Core\Domains\HelpDesk\Services\FileService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketCommentService;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use Core\Domains\HelpDesk\Services\TicketService;

class HelpDeskServiceLocator
{
    private static TicketService    $ticketService;
    private static TicketFactory    $ticketFactory;
    private static TicketRepository $ticketRepository;

    private static TicketCategoryService    $ticketCategoryService;
    private static TicketCategoryFactory    $ticketCategoryFactory;
    private static TicketCategoryRepository $ticketCategoryRepository;

    private static TicketServiceService    $ticketServiceService;
    private static TicketServiceFactory    $ticketServiceFactory;
    private static TicketServiceRepository $ticketServiceRepository;

    private static TicketCommentService    $ticketCommentService;
    private static TicketCommentFactory    $ticketCommentFactory;
    private static TicketCommentRepository $ticketCommentRepository;

    private static FileService $fileService;

    // ========== ТИКЕТЫ ==========

    public static function TicketService(): TicketService
    {
        if ( ! isset(self::$ticketService)) {
            self::$ticketService = new TicketService(
                self::TicketRepository(),
            );
        }

        return self::$ticketService;
    }

    public static function TicketFactory(): TicketFactory
    {
        if ( ! isset(self::$ticketFactory)) {
            self::$ticketFactory = new TicketFactory();
        }

        return self::$ticketFactory;
    }

    public static function TicketRepository(): TicketRepository
    {
        if ( ! isset(self::$ticketRepository)) {
            self::$ticketRepository = new TicketRepository(
                self::TicketFactory(),
            );
        }

        return self::$ticketRepository;
    }

    // ========== КАТЕГОРИИ ТИКЕТОВ ==========

    public static function TicketCategoryService(): TicketCategoryService
    {
        if ( ! isset(self::$ticketCategoryService)) {
            self::$ticketCategoryService = new TicketCategoryService(
                self::TicketCategoryRepository(),
            );
        }

        return self::$ticketCategoryService;
    }

    public static function TicketCategoryFactory(): TicketCategoryFactory
    {
        if ( ! isset(self::$ticketCategoryFactory)) {
            self::$ticketCategoryFactory = new TicketCategoryFactory();
        }

        return self::$ticketCategoryFactory;
    }

    public static function TicketCategoryRepository(): TicketCategoryRepository
    {
        if ( ! isset(self::$ticketCategoryRepository)) {
            self::$ticketCategoryRepository = new TicketCategoryRepository(
                self::TicketCategoryFactory(),
            );
        }

        return self::$ticketCategoryRepository;
    }

    // ========== УСЛУГИ (SERVICES) ==========

    public static function TicketServiceService(): TicketServiceService
    {
        if ( ! isset(self::$ticketServiceService)) {
            self::$ticketServiceService = new TicketServiceService(
                self::TicketServiceRepository(),
            );
        }

        return self::$ticketServiceService;
    }

    public static function TicketServiceFactory(): TicketServiceFactory
    {
        if ( ! isset(self::$ticketServiceFactory)) {
            self::$ticketServiceFactory = new TicketServiceFactory();
        }

        return self::$ticketServiceFactory;
    }

    public static function TicketServiceRepository(): TicketServiceRepository
    {
        if ( ! isset(self::$ticketServiceRepository)) {
            self::$ticketServiceRepository = new TicketServiceRepository(
                self::TicketServiceFactory(),
            );
        }

        return self::$ticketServiceRepository;
    }

    // ========== КОММЕНТАРИИ ==========

    public static function TicketCommentService(): TicketCommentService
    {
        if ( ! isset(self::$ticketCommentService)) {
            self::$ticketCommentService = new TicketCommentService(
                self::TicketCommentRepository(),
            );
        }

        return self::$ticketCommentService;
    }

    public static function TicketCommentFactory(): TicketCommentFactory
    {
        if ( ! isset(self::$ticketCommentFactory)) {
            self::$ticketCommentFactory = new TicketCommentFactory();
        }

        return self::$ticketCommentFactory;
    }

    public static function TicketCommentRepository(): TicketCommentRepository
    {
        if ( ! isset(self::$ticketCommentRepository)) {
            self::$ticketCommentRepository = new TicketCommentRepository(
                self::TicketCommentFactory(),
            );
        }

        return self::$ticketCommentRepository;
    }

    public static function FileService(): Services\FileService
    {
        if ( ! isset(self::$fileService)) {
            self::$fileService = new FileService(
                FileLocator::FileService(),
            );
        }

        return self::$fileService;
    }
}
