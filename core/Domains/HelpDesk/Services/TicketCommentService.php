<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\HelpDesk\Models\TicketCommentDTO;
use Core\Domains\HelpDesk\Repositories\TicketCommentRepository;
use Core\Domains\HelpDesk\Responses\TicketCommentSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCommentSearcher;

readonly class TicketCommentService
{
    public function __construct(
        private TicketCommentRepository $ticketCommentRepository,
    )
    {
    }

    public function search(TicketCommentSearcher $searcher): TicketCommentSearchResponse
    {
        return $this->ticketCommentRepository->search($searcher);
    }

    public function getById(?int $id): ?TicketCommentDTO
    {
        return $this->ticketCommentRepository->getById($id);
    }

    public function save(TicketCommentDTO $comment): TicketCommentDTO
    {
        return $this->ticketCommentRepository->save($comment);
    }

    public function deleteById(?int $id): bool
    {
        return $this->ticketCommentRepository->deleteById($id);
    }
}
