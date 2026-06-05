<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Category;

use Core\App\HelpDesk\Category\DeleteCommand;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketService;
use RuntimeException;
use Tests\TestCase;

class DeleteCommandTest extends TestCase
{
    private TicketCategoryService $ticketCategoryService;
    private TicketCatalogService  $ticketServiceService;
    private TicketService         $ticketService;
    private DeleteCommand         $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketCategoryService = $this->createMock(TicketCategoryService::class);
        $this->ticketServiceService  = $this->createMock(TicketCatalogService::class);
        $this->ticketService         = $this->createMock(TicketService::class);
        $this->command               = new DeleteCommand(
            $this->ticketCategoryService,
            $this->ticketServiceService,
            $this->ticketService,
        );
    }

    public function test_execute_deletes_when_no_services_and_no_tickets(): void
    {
        $category = (new TicketCategoryEntity)->setId(1)->setName('Test');

        $emptyServiceResponse = new TicketServiceSearchResponse;
        $emptyTicketResponse  = new TicketSearchResponse;

        $this->ticketServiceService->method('search')->willReturn($emptyServiceResponse);
        $this->ticketService->method('search')->willReturn($emptyTicketResponse);

        $this->ticketCategoryService->expects($this->once())
            ->method('deleteById')
            ->with(1)
        ;

        $this->command->execute($category);
    }

    public function test_execute_throws_when_services_exist(): void
    {
        $category = (new TicketCategoryEntity)->setId(1)->setName('Test');

        $serviceResponse = new TicketServiceSearchResponse;
        $serviceResponse->setTotal(1);

        $this->ticketServiceService->method('search')->willReturn($serviceResponse);

        $this->ticketCategoryService->expects($this->never())->method('deleteById');

        $this->expectException(RuntimeException::class);

        $this->command->execute($category);
    }

    public function test_execute_throws_when_tickets_exist(): void
    {
        $category = (new TicketCategoryEntity)->setId(1)->setName('Test');

        $emptyServiceResponse = new TicketServiceSearchResponse;
        $ticketResponse       = new TicketSearchResponse;
        $ticketResponse->setTotal(1);

        $this->ticketServiceService->method('search')->willReturn($emptyServiceResponse);
        $this->ticketService->method('search')->willReturn($ticketResponse);

        $this->ticketCategoryService->expects($this->never())->method('deleteById');

        $this->expectException(RuntimeException::class);

        $this->command->execute($category);
    }
}
