<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\HelpDesk\Models\TicketCommentEntity;
use Core\Domains\HelpDesk\Responses\TicketCommentSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCommentSearcher;
use Core\Domains\HelpDesk\TicketCommentRepositoryInterface;

readonly class TicketCommentService
{
    public function __construct(
        private TicketCommentRepositoryInterface $ticketCommentRepository,
    )
    {
    }

    public function search(TicketCommentSearcher $searcher): TicketCommentSearchResponse
    {
        return $this->ticketCommentRepository->search($searcher);
    }

    public function getById(?int $id): ?TicketCommentEntity
    {
        return $this->ticketCommentRepository->getById($id);
    }

    public function save(TicketCommentEntity $comment): TicketCommentEntity
    {
        return $this->ticketCommentRepository->save($comment);
    }

    public function deleteById(?int $id): bool
    {
        return $this->ticketCommentRepository->deleteById($id);
    }
}
