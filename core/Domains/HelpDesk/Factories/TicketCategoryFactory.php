<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Factories;

use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;

class TicketCategoryFactory
{
    public function makeDefault(): TicketCategoryEntity
    {
        return (new TicketCategoryEntity())
            ->setIsActive(true)
            ->setType(TicketTypeEnum::INCIDENT)
            ->setSortOrder(999);
    }
}
