<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk;

use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Responses\TicketCategorySearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;

interface TicketCategoryRepositoryInterface
{
    public function search(TicketCategorySearcher $searcher): TicketCategorySearchResponse;

    public function save(TicketCategoryEntity $category): TicketCategoryEntity;

    public function getById(?int $id): ?TicketCategoryEntity;

    public function deleteById(?int $id): bool;
}
