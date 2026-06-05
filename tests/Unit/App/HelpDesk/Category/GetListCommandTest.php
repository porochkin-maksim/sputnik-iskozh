<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Category;

use Core\App\HelpDesk\Category\GetListCommand;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Responses\TicketCategorySearchResponse;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private TicketCategoryService $ticketCategoryService;
    private GetListCommand        $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketCategoryService = $this->createMock(TicketCategoryService::class);
        $this->command               = new GetListCommand($this->ticketCategoryService);
    }

    public function test_execute_returns_all_categories_sorted(): void
    {
        $items = new TicketCategoryCollection;

        $response = new TicketCategorySearchResponse;
        $response->setItems($items);

        $this->ticketCategoryService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute();

        $this->assertSame($items, $result);
    }
}
