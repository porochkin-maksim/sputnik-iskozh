<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Searchers;

use App\Models\HelpDesk\TicketCategory;
use Core\Db\Searcher\BaseSearcher;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;

class TicketCategorySearcher extends BaseSearcher
{
    public function useOrderSort(): static
    {
        $this
            ->setSortOrderProperty(TicketCategory::TYPE, SearcherInterface::SORT_ORDER_ASC)
            ->setSortOrderProperty(TicketCategory::SORT_ORDER, SearcherInterface::SORT_ORDER_ASC)
        ;

        return $this;
    }

    public function setWithServices(): static
    {
        $this->with[] = TicketCategory::RELATION_SERVICES;

        return $this;
    }

    public function setType(TicketTypeEnum $type): static
    {
        $this->addWhere(TicketCategory::TYPE, SearcherInterface::EQUALS, $type->value);

        return $this;
    }

    public function setCode(string $code): static
    {
        $this->addWhere(TicketCategory::CODE, SearcherInterface::EQUALS, $code);

        return $this;
    }

    public function setActive(bool $isActive): static
    {
        $this->addWhere(TicketCategory::IS_ACTIVE, SearcherInterface::EQUALS, $isActive);

        return $this;
    }
}
