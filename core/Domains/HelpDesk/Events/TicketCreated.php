<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Events;

readonly class TicketCreated
{
    public function __construct(
        public int $ticketId,
    )
    {
    }
}
