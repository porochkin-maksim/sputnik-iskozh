<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Service;

use Core\App\HelpDesk\Service\CreateCommand;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Factories\TicketServiceFactory;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Tests\TestCase;

class CreateCommandTest extends TestCase
{
    private TicketCatalogService $ticketServiceService;
    private TicketServiceFactory $ticketServiceFactory;
    private CreateCommand        $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketServiceService = $this->createMock(TicketCatalogService::class);
        $this->ticketServiceFactory = $this->createMock(TicketServiceFactory::class);
        $this->command              = new CreateCommand(
            $this->ticketServiceService,
            $this->ticketServiceFactory,
        );
    }

    public function test_execute_returns_service_with_incremented_sort_order(): void
    {
        $last = (new TicketServiceEntity)->setSortOrder(30);

        $response = new TicketServiceSearchResponse;
        $response->setItems(new TicketServiceCollection([$last]));

        $this->ticketServiceService->method('search')->willReturn($response);

        $this->ticketServiceFactory->method('makeDefault')
            ->willReturn(new TicketServiceEntity)
        ;

        $result = $this->command->execute(1);

        $this->assertSame(40, $result->getSortOrder());
        $this->assertSame(1, $result->getCategoryId());
        $this->assertTrue($result->getIsActive());
    }

    public function test_execute_returns_service_with_default_sort_order(): void
    {
        $response = new TicketServiceSearchResponse;
        $response->setItems(new TicketServiceCollection);

        $this->ticketServiceService->method('search')->willReturn($response);

        $this->ticketServiceFactory->method('makeDefault')
            ->willReturn(new TicketServiceEntity)
        ;

        $result = $this->command->execute(1);

        $this->assertSame(10, $result->getSortOrder());
    }
}
