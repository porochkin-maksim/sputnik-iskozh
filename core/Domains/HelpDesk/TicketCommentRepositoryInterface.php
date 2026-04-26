<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk;

use Core\Domains\HelpDesk\Models\TicketCommentEntity;
use Core\Domains\HelpDesk\Responses\TicketCommentSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCommentSearcher;

interface TicketCommentRepositoryInterface
{
    public function search(TicketCommentSearcher $searcher): TicketCommentSearchResponse;

    public function save(TicketCommentEntity $comment): TicketCommentEntity;

    public function getById(?int $id): ?TicketCommentEntity;

    public function deleteById(?int $id): bool;
}
