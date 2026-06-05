<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Service;

use Core\App\HelpDesk\Service\DeleteCommand;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketService;
use RuntimeException;
use Tests\TestCase;

class DeleteCommandTest extends TestCase
{
    private TicketCatalogService $ticketServiceService;
    private TicketService        $ticketService;
    private DeleteCommand        $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketServiceService = $this->createMock(TicketCatalogService::class);
        $this->ticketService        = $this->createMock(TicketService::class);
        $this->command              = new DeleteCommand(
            $this->ticketServiceService,
            $this->ticketService,
        );
    }

    public function test_execute_deletes_when_no_tickets(): void
    {
        $service = (new TicketServiceEntity)->setId(1)->setName('Test');

        $emptyResponse = new TicketSearchResponse;

        $this->ticketService->method('search')->willReturn($emptyResponse);

        $this->ticketServiceService->expects($this->once())
            ->method('deleteById')
            ->with(1)
        ;

        $this->command->execute($service);
    }

    public function test_execute_throws_when_tickets_exist(): void
    {
        $service = (new TicketServiceEntity)->setId(1)->setName('Test');

        $response = new TicketSearchResponse;
        $response->setTotal(1);

        $this->ticketService->method('search')->willReturn($response);

        $this->ticketServiceService->expects($this->never())->method('deleteById');

        $this->expectException(RuntimeException::class);

        $this->command->execute($service);
    }
}
