<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Factories;

use Core\Domains\HelpDesk\Models\TicketEntity;

class TicketFactory
{
    public function makeDefault(): TicketEntity
    {
        return new TicketEntity();
    }
}
