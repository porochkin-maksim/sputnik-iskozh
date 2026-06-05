<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Category;

use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Factories\TicketCategoryFactory;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\Services\TicketCategoryService;

readonly class CreateCommand
{
    public function __construct(
        private TicketCategoryService $ticketCategoryService,
        private TicketCategoryFactory $ticketCategoryFactory,
    )
    {
    }

    public function execute(int $type): TicketCategoryEntity
    {
        $searcher = new TicketCategorySearcher();
        $searcher
            ->setType(TicketTypeEnum::tryFrom($type))
            ->setSortOrderPropertyIdDesc()
            ->setLimit(1)
        ;

        $lastCategory = $this->ticketCategoryService->search(
            $searcher,
        )->getItems()->first();

        return $this->ticketCategoryFactory->makeDefault()
            ->setSortOrder((int) $lastCategory?->getSortOrder() + 10)
        ;
    }
}
