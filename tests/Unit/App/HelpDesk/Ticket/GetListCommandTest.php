<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\GetListCommand;
use Core\Domains\HelpDesk\Collection\TicketCollection;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\Services\TicketService;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private TicketService  $ticketService;
    private GetListCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketService = $this->createMock(TicketService::class);

        $this->command = new GetListCommand(
            $this->ticketService,
        );
    }

    public function test_execute_with_default_sort(): void
    {
        $response = new TicketSearchResponse;
        $response->setItems(new TicketCollection);

        $this->ticketService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(TicketSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, null, null, null, null);

        $this->assertSame($response, $result);
    }

    public function test_execute_with_asc_sort(): void
    {
        $response = new TicketSearchResponse;
        $response->setItems(new TicketCollection);

        $this->ticketService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(TicketSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, 'description', 'asc', null, null, null, null);

        $this->assertSame($response, $result);
    }

    public function test_execute_filters_by_category(): void
    {
        $response = new TicketSearchResponse;
        $response->setItems(new TicketCollection);

        $this->ticketService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(TicketSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, 5, null, null, null);

        $this->assertSame($response, $result);
    }

    public function test_execute_filters_by_service(): void
    {
        $response = new TicketSearchResponse;
        $response->setItems(new TicketCollection);

        $this->ticketService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(TicketSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, null, 3, null, null);

        $this->assertSame($response, $result);
    }

    public function test_execute_filters_by_priority(): void
    {
        $response = new TicketSearchResponse;
        $response->setItems(new TicketCollection);

        $this->ticketService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(TicketSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, null, null, 1, null);

        $this->assertSame($response, $result);
    }

    public function test_execute_filters_by_status(): void
    {
        $response = new TicketSearchResponse;
        $response->setItems(new TicketCollection);

        $this->ticketService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(TicketSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, null, null, null, 2);

        $this->assertSame($response, $result);
    }
}
