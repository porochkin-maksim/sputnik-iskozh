<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Category;

use Core\App\HelpDesk\Category\SaveCommand;
use Core\App\HelpDesk\Category\SaveValidator;
use Core\Contracts\StringServiceInterface;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private StringServiceInterface $stringService;
    private TicketCategoryService  $ticketCategoryService;
    private SaveValidator          $saveValidator;
    private SaveCommand            $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stringService         = $this->createMock(StringServiceInterface::class);
        $this->ticketCategoryService = $this->createMock(TicketCategoryService::class);
        $this->saveValidator         = $this->createMock(SaveValidator::class);
        $this->command               = new SaveCommand(
            $this->stringService,
            $this->ticketCategoryService,
            $this->saveValidator,
        );
    }

    public function test_execute_creates_new_category(): void
    {
        $saved = (new TicketCategoryEntity)->setId(1);

        $this->stringService->method('slug')->with('New Cat')->willReturn('new-cat');

        $this->saveValidator->method('validate');

        $this->ticketCategoryService->expects($this->once())
            ->method('save')
            ->willReturn($saved)
        ;

        $result = $this->command->execute(null, 1, 'New Cat', '', 10, true);

        $this->assertSame($saved, $result);
    }

    public function test_execute_updates_existing_category(): void
    {
        $existing = (new TicketCategoryEntity)->setId(5);
        $saved    = (new TicketCategoryEntity)->setId(5);

        $this->ticketCategoryService->method('getById')->with(5)->willReturn($existing);

        $this->stringService->method('slug')->with('Updated')->willReturn('updated');

        $this->saveValidator->method('validate');

        $this->ticketCategoryService->expects($this->once())
            ->method('save')
            ->willReturn($saved)
        ;

        $result = $this->command->execute(5, 2, 'Updated', '', 20, false);

        $this->assertSame($saved, $result);
    }

    public function test_execute_returns_null_when_category_not_found(): void
    {
        $this->ticketCategoryService->method('getById')->willReturn(null);

        $this->ticketCategoryService->expects($this->never())->method('save');

        $result = $this->command->execute(999, 1, 'Ghost', '', 10, true);

        $this->assertNull($result);
    }

    public function test_execute_throws_when_validation_fails(): void
    {
        $this->saveValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['name' => ['error']]))
        ;

        $this->ticketCategoryService->expects($this->never())->method('save');

        $this->expectException(ValidationException::class);

        $this->command->execute(null, 1, '', '', 10, true);
    }
}
