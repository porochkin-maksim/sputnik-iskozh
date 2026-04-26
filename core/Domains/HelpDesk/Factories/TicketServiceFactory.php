<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Factories;

use Core\Domains\HelpDesk\Models\TicketServiceEntity;

class TicketServiceFactory
{
    public function makeDefault(): TicketServiceEntity
    {
        return new TicketServiceEntity();
    }
}
