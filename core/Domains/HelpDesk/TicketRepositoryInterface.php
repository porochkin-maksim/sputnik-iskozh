<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk;

use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;

interface TicketRepositoryInterface
{
    public function search(TicketSearcher $searcher): TicketSearchResponse;

    public function save(TicketEntity $ticket): TicketEntity;

    public function getById(?int $id): ?TicketEntity;

    public function deleteById(?int $id): bool;
}
