<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Services\TicketService;

readonly class DeleteCommand
{
    public function __construct(
        private TicketService $ticketService,
    )
    {
    }

    public function execute(TicketEntity $ticket): void
    {
        $this->ticketService->deleteById($ticket->getId());
    }
}
