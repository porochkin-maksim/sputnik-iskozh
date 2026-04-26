<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Service;

use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\Services\TicketCatalogService;

readonly class GetListCommand
{
    public function __construct(
        private TicketCatalogService $ticketServiceService,
    )
    {
    }

    public function execute(int $categoryId): TicketServiceCollection
    {
        return $this->ticketServiceService->search(
            (new TicketServiceSearcher())
                ->setCategoryId($categoryId)
                ->useOrderSort(),
        )->getItems();
    }
}
