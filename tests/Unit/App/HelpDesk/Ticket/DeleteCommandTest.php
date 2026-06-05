<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\DeleteCommand;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Services\TicketService;
use Tests\TestCase;

class DeleteCommandTest extends TestCase
{
    private TicketService $ticketService;
    private DeleteCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketService = $this->createMock(TicketService::class);
        $this->command = new DeleteCommand($this->ticketService);
    }

    public function test_execute_deletes_ticket(): void
    {
        $ticket = (new TicketEntity)->setId(5);

        $this->ticketService->expects($this->once())
            ->method('deleteById')
            ->with(5);

        $this->command->execute($ticket);
    }
}
