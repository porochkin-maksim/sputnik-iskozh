<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Searchers;

use App\Models\HelpDesk\Ticket;
use Core\Db\Searcher\BaseSearcher;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;

class TicketSearcher extends BaseSearcher
{
    public function __construct()
    {
        $this->with[] = Ticket::RELATION_USER;
        $this->with[] = Ticket::RELATION_ACCOUNT;
        $this->with[] = Ticket::RELATION_CATEGORY;
        $this->with[] = Ticket::RELATION_SERVICE;
        $this->with[] = Ticket::RELATION_FILES;
        $this->with[] = Ticket::RELATION_RESULT_FILES;
    }

    public function setServiceId(?int $id): static
    {
        $this->addWhere(Ticket::SERVICE_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setCategoryId(?int $id): static
    {
        $this->addWhere(Ticket::CATEGORY_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setPriority(TicketPriorityEnum $priority): static
    {
        $this->addWhere(Ticket::PRIORITY, SearcherInterface::EQUALS, $priority->value);

        return $this;
    }

    public function setStatus(TicketStatusEnum $status): static
    {
        $this->addWhere(Ticket::STATUS, SearcherInterface::EQUALS, $status->value);

        return $this;
    }
}
