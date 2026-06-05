<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Category;

use Core\App\HelpDesk\Category\CreateCommand;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Factories\TicketCategoryFactory;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Responses\TicketCategorySearchResponse;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Tests\TestCase;

class CreateCommandTest extends TestCase
{
    private TicketCategoryService $ticketCategoryService;
    private TicketCategoryFactory $ticketCategoryFactory;
    private CreateCommand         $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketCategoryService = $this->createMock(TicketCategoryService::class);
        $this->ticketCategoryFactory = $this->createMock(TicketCategoryFactory::class);
        $this->command               = new CreateCommand(
            $this->ticketCategoryService,
            $this->ticketCategoryFactory,
        );
    }

    public function test_execute_returns_category_with_incremented_sort_order(): void
    {
        $last = (new TicketCategoryEntity)->setSortOrder(50);

        $response = new TicketCategorySearchResponse;
        $response->setItems(new TicketCategoryCollection([$last]));

        $this->ticketCategoryService->method('search')->willReturn($response);

        $this->ticketCategoryFactory->method('makeDefault')
            ->willReturn(new TicketCategoryEntity)
        ;

        $result = $this->command->execute(1);

        $this->assertSame(60, $result->getSortOrder());
    }

    public function test_execute_returns_category_with_default_sort_order(): void
    {
        $response = new TicketCategorySearchResponse;
        $response->setItems(new TicketCategoryCollection);

        $this->ticketCategoryService->method('search')->willReturn($response);

        $this->ticketCategoryFactory->method('makeDefault')
            ->willReturn(new TicketCategoryEntity)
        ;

        $result = $this->command->execute(1);

        $this->assertSame(10, $result->getSortOrder());
    }
}
