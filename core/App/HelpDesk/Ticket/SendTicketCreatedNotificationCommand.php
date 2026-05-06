<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

readonly class SendTicketCreatedNotificationCommand
{
    public function __construct(
        public int $ticketId,
    )
    {
    }
}
