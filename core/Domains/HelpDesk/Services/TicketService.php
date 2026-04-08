<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\HelpDesk\Models\TicketDTO;
use Core\Domains\HelpDesk\Repositories\TicketRepository;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;

readonly class TicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
    )
    {
    }

    public function search(TicketSearcher $searcher): TicketSearchResponse
    {
        return $this->ticketRepository->search($searcher);
    }

    public function getById(?int $id): ?TicketDTO
    {
        return $this->ticketRepository->getById($id);
    }

    public function save(TicketDTO $ticket): TicketDTO
    {
        return $this->ticketRepository->save($ticket);
    }

    public function deleteById(?int $id): bool
    {
        return $this->ticketRepository->deleteById($id);
    }
}
