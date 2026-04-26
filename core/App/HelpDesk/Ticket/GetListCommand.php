<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use App\Models\HelpDesk\Ticket;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Repositories\SearcherInterface;

readonly class GetListCommand
{
    public function __construct(
        private TicketService $ticketService,
    )
    {
    }

    public function execute(
        ?int    $limit,
        ?int    $offset,
        ?string $sortField,
        ?string $sortOrder,
        ?int    $categoryId,
        ?int    $serviceId,
        ?int    $priority,
        ?int    $status,
    ): TicketSearchResponse
    {
        $searcher = new TicketSearcher();
        $searcher
            ->setLimit($limit)
            ->setOffset($offset)
        ;

        if ($sortField && $sortOrder) {
            $searcher->setSortOrderProperty(
                $sortField,
                $sortOrder === 'asc' ? SearcherInterface::SORT_ORDER_ASC : SearcherInterface::SORT_ORDER_DESC,
            );
        }
        else {
            $searcher->setSortOrderProperty(Ticket::ID, SearcherInterface::SORT_ORDER_DESC);
        }

        if ($categoryId) {
            $searcher->setCategoryId($categoryId);
        }

        if ($serviceId) {
            $searcher->setServiceId($serviceId);
        }

        if ($priority) {
            $searcher->setPriority(TicketPriorityEnum::tryFrom($priority));
        }

        if ($status) {
            $searcher->setStatus(TicketStatusEnum::tryFrom($status));
        }

        return $this->ticketService->search($searcher);
    }
}
