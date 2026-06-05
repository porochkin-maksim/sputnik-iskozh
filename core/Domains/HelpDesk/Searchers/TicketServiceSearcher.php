<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Searchers;

use App\Models\HelpDesk\TicketService;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class TicketServiceSearcher extends BaseSearcher
{
    public function useOrderSort(): static
    {
        $this->setSortOrderProperty(TicketService::SORT_ORDER, SearcherInterface::SORT_ORDER_ASC);

        return $this;
    }

    public function setCategoryId(int $id): static
    {
        $this->addWhere(TicketService::CATEGORY_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setCode(?string $code): static
    {
        $this->addWhere(TicketService::CODE, SearcherInterface::EQUALS, $code);

        return $this;
    }
}
