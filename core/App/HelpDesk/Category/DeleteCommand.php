<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Category;

use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketService;
use RuntimeException;

readonly class DeleteCommand
{
    public function __construct(
        private TicketCategoryService $ticketCategoryService,
        private TicketCatalogService  $ticketServiceService,
        private TicketService         $ticketService,
    )
    {
    }

    /**
     * @throws RuntimeException
     */
    public function execute(TicketCategoryEntity $category): void
    {
        $serviceSearcher = new TicketServiceSearcher();
        $serviceSearcher->setCategoryId($category->getId());
        $services = $this->ticketServiceService->search($serviceSearcher);

        if ($services->getTotal() > 0) {
            throw new RuntimeException(
                sprintf('Невозможно удалить категорию "%s", так как у неё есть связанные услуги', $category->getName()),
            );
        }

        $ticketSearcher = new TicketSearcher();
        $ticketSearcher->setCategoryId($category->getId());
        $tickets = $this->ticketService->search($ticketSearcher);

        if ($tickets->getTotal() > 0) {
            throw new RuntimeException(
                sprintf('Невозможно удалить категорию "%s", так как у неё есть связанные завки', $category->getName()),
            );
        }

        $this->ticketCategoryService->deleteById($category->getId());
    }
}
