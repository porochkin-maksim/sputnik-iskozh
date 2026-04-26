<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\TicketRepositoryInterface;

readonly class TicketService
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
    )
    {
    }

    public function search(TicketSearcher $searcher): TicketSearchResponse
    {
        return $this->ticketRepository->search($searcher);
    }

    public function getById(?int $id): ?TicketEntity
    {
        return $this->ticketRepository->getById($id);
    }

    public function save(TicketEntity $ticket): TicketEntity
    {
        return $this->ticketRepository->save($ticket);
    }

    public function deleteById(?int $id): bool
    {
        return $this->ticketRepository->deleteById($id);
    }
}
