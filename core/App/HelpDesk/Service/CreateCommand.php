<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Service;

use Core\Domains\HelpDesk\Factories\TicketServiceFactory;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\Services\TicketCatalogService;

readonly class CreateCommand
{
    public function __construct(
        private TicketCatalogService $ticketServiceService,
        private TicketServiceFactory $ticketServiceFactory,
    )
    {
    }

    public function execute(int $categoryId): TicketServiceEntity
    {
        $lastService = $this->ticketServiceService->search(
            (new TicketServiceSearcher())
                ->setCategoryId($categoryId)
                ->setSortOrderPropertyIdDesc()
                ->setLimit(1),
        )->getItems()->first();

        return $this->ticketServiceFactory->makeDefault()
            ->setCategoryId($categoryId)
            ->setSortOrder((int) $lastService?->getSortOrder() + 10)
            ->setIsActive(true)
        ;
    }
}
