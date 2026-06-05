<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Service;

use Core\App\HelpDesk\Service\SaveCommand;
use Core\App\HelpDesk\Service\SaveValidator;
use Core\Contracts\StringServiceInterface;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private StringServiceInterface $stringService;
    private TicketCatalogService   $ticketServiceService;
    private SaveValidator          $saveValidator;
    private SaveCommand            $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stringService        = $this->createMock(StringServiceInterface::class);
        $this->ticketServiceService = $this->createMock(TicketCatalogService::class);
        $this->saveValidator        = $this->createMock(SaveValidator::class);
        $this->command              = new SaveCommand(
            $this->stringService,
            $this->ticketServiceService,
            $this->saveValidator,
        );
    }

    public function test_execute_creates_new_service(): void
    {
        $saved = (new TicketServiceEntity)->setId(1);

        $this->stringService->method('slug')->with('New Svc')->willReturn('new-svc');

        $this->saveValidator->method('validate');

        $this->ticketServiceService->expects($this->once())
            ->method('save')
            ->willReturn($saved)
        ;

        $result = $this->command->execute(null, 1, 'New Svc', '', 10, true);

        $this->assertSame($saved, $result);
    }

    public function test_execute_updates_existing_service(): void
    {
        $existing = (new TicketServiceEntity)->setId(5);
        $saved    = (new TicketServiceEntity)->setId(5);

        $this->ticketServiceService->method('getById')->with(5)->willReturn($existing);

        $this->stringService->method('slug')->with('Updated')->willReturn('updated');

        $this->saveValidator->method('validate');

        $this->ticketServiceService->expects($this->once())
            ->method('save')
            ->willReturn($saved)
        ;

        $result = $this->command->execute(5, 2, 'Updated', '', 20, false);

        $this->assertSame($saved, $result);
    }

    public function test_execute_returns_null_when_service_not_found(): void
    {
        $this->ticketServiceService->method('getById')->willReturn(null);

        $this->ticketServiceService->expects($this->never())->method('save');

        $result = $this->command->execute(999, 1, 'Ghost', '', 10, true);

        $this->assertNull($result);
    }

    public function test_execute_throws_when_validation_fails(): void
    {
        $this->saveValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['name' => ['error']]))
        ;

        $this->ticketServiceService->expects($this->never())->method('save');

        $this->expectException(ValidationException::class);

        $this->command->execute(null, 1, '', '', 10, true);
    }
}
