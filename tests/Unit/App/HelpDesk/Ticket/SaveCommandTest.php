<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\SaveCommand;
use Core\App\HelpDesk\Ticket\UpdateCommand;
use Core\App\HelpDesk\Ticket\UpdateInput;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Services\TicketService;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private TicketService $ticketService;
    private UpdateCommand $updateCommand;
    private SaveCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketService = $this->createMock(TicketService::class);
        $this->updateCommand = $this->createMock(UpdateCommand::class);

        $this->command = new SaveCommand(
            $this->ticketService,
            $this->updateCommand,
        );
    }

    public function test_execute_returns_null_when_ticket_not_found(): void
    {
        $this->ticketService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $result = $this->command->execute(
            id      : 999, description: 'desc', result: null, type: 1, categoryId: 1, serviceId: 1,
            priority: 2, status: 1, contactName: 'Ivan', contactPhone: null, contactEmail: null,
            userId  : 1, accountId: 1, files: [], resultFiles: [],
        );

        $this->assertNull($result);
    }

    public function test_execute_delegates_to_update_command_when_found(): void
    {
        $ticket = (new TicketEntity)->setId(1);

        $this->ticketService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($ticket)
        ;

        $this->updateCommand->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(UpdateInput::class))
            ->willReturn($ticket)
        ;

        $result = $this->command->execute(
            id      : 1, description: 'desc', result: null, type: 1, categoryId: 1, serviceId: 1,
            priority: 2, status: 1, contactName: 'Ivan', contactPhone: null, contactEmail: null,
            userId  : 1, accountId: 1, files: [], resultFiles: [],
        );

        $this->assertSame($ticket, $result);
    }
}
