<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\HelpDesk;

use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;

readonly class IndexPageController
{
    public function __construct(
        private TicketCategoryService $ticketCategoryService,
        private TicketCatalogService  $ticketCatalogService,
    )
    {
    }

    public function __invoke()
    {
        $categories = $this->ticketCategoryService->search(new TicketCategorySearcher())->getItems();
        $services   = $this->ticketCatalogService->search(new TicketServiceSearcher())->getItems();

        return view('pages.admin.help-desk.index', compact('categories', 'services'));
    }
}
