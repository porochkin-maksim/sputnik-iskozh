<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Category;

use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\Services\TicketCategoryService;

readonly class GetListCommand
{
    public function __construct(
        private TicketCategoryService $ticketCategoryService,
    )
    {
    }

    public function execute(): TicketCategoryCollection
    {
        $searcher = new TicketCategorySearcher();
        $searcher->useOrderSort();

        return $this->ticketCategoryService->search($searcher)->getItems();
    }
}
