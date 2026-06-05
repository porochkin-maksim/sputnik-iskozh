<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Service;

use Core\App\HelpDesk\Service\GetListCommand;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private TicketCatalogService $ticketServiceService;
    private GetListCommand       $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketServiceService = $this->createMock(TicketCatalogService::class);
        $this->command              = new GetListCommand($this->ticketServiceService);
    }

    public function test_execute_returns_services_filtered_by_category(): void
    {
        $items = new TicketServiceCollection;

        $response = new TicketServiceSearchResponse;
        $response->setItems($items);

        $this->ticketServiceService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(1);

        $this->assertSame($items, $result);
    }
}
