<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Services\TicketService;
use RuntimeException;

readonly class DeleteCommand
{
    public function __construct(
        private TicketService $ticketService,
    )
    {
    }

    public function execute(TicketEntity $ticket): void
    {
        if ($ticket->getStatus() === TicketStatusEnum::CLOSED || $ticket->getStatus() === TicketStatusEnum::REJECTED) {
            throw new RuntimeException('Нельзя удалить закрытую или отклонённую заявку');
        }

        $this->ticketService->deleteById($ticket->getId());
    }
}
