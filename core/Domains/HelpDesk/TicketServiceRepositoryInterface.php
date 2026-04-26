<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk;

use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;

interface TicketServiceRepositoryInterface
{
    public function search(TicketServiceSearcher $searcher): TicketServiceSearchResponse;

    public function save(TicketServiceEntity $service): TicketServiceEntity;

    public function getById(?int $id): ?TicketServiceEntity;

    public function deleteById(?int $id): bool;
}
