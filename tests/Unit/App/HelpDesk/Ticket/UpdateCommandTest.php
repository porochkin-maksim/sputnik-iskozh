<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\UpdateCommand;
use Core\App\HelpDesk\Ticket\UpdateInput;
use Core\App\HelpDesk\Ticket\UpdateValidator;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Services\FileService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class UpdateCommandTest extends TestCase
{
    private TicketService         $ticketService;
    private TicketCategoryService $categoryService;
    private TicketCatalogService  $serviceService;
    private FileService           $fileService;
    private UpdateValidator       $validator;
    private UpdateCommand         $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketService   = $this->createMock(TicketService::class);
        $this->categoryService = $this->createMock(TicketCategoryService::class);
        $this->serviceService  = $this->createMock(TicketCatalogService::class);
        $this->fileService     = $this->createMock(FileService::class);
        $this->validator       = $this->createMock(UpdateValidator::class);

        $this->command = new UpdateCommand(
            $this->ticketService,
            $this->categoryService,
            $this->serviceService,
            $this->fileService,
            $this->validator,
        );
    }

    private function makeInput(): UpdateInput
    {
        return new UpdateInput(
            id          : 1,
            description : 'updated description',
            result      : null,
            type        : 2,
            categoryId  : 10,
            serviceId   : 20,
            priority    : 2,
            status      : 2,
            contactName : 'Petr',
            contactPhone: '+79991112233',
            contactEmail: 'petr@test.com',
            userId      : 3,
            accountId   : 4,
            files       : [],
            resultFiles : [],
        );
    }

    public function test_execute_updates_ticket_on_happy_path(): void
    {
        $input    = $this->makeInput();
        $category = (new TicketCategoryEntity)->setId(10);
        $service  = (new TicketServiceEntity)->setId(20);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($input)
        ;

        $this->categoryService->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($category)
        ;

        $this->serviceService->expects($this->once())
            ->method('getById')
            ->with(20)
            ->willReturn($service)
        ;

        $this->ticketService->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(TicketEntity::class))
            ->willReturnCallback(fn(TicketEntity $t) => $t->setId(1))
        ;

        $this->fileService->expects($this->once())
            ->method('storeTicketFiles')
            ->with([], 1)
        ;

        $this->fileService->expects($this->once())
            ->method('storeTicketResultFiles')
            ->with([], 1)
        ;

        $result = $this->command->execute($input);

        $this->assertInstanceOf(TicketEntity::class, $result);
        $this->assertSame(1, $result->getId());
        $this->assertSame(TicketTypeEnum::tryFrom(2), $result->getType());
        $this->assertSame(TicketStatusEnum::tryFrom(2), $result->getStatus());
        $this->assertSame(TicketPriorityEnum::tryFrom(2), $result->getPriority());
    }

    public function test_execute_sets_resolved_at_for_closed_status(): void
    {
        $input = new UpdateInput(1, 'desc', 'done', 1, 10, 20, 2, 4, 'Ivan', '+79991234567', 'ivan@test.com', 3, 4, [], []);

        $this->validator->expects($this->once())->method('validate');
        $this->categoryService->expects($this->once())->method('getById')->willReturn(new TicketCategoryEntity);
        $this->serviceService->expects($this->once())->method('getById')->willReturn(new TicketServiceEntity);

        $this->ticketService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(TicketEntity $t) => $t->getResolvedAt() !== null))
            ->willReturnCallback(fn(TicketEntity $t) => $t->setId(1))
        ;

        $this->fileService->expects($this->once())->method('storeTicketFiles');
        $this->fileService->expects($this->once())->method('storeTicketResultFiles');

        $this->command->execute($input);
    }

    public function test_execute_sets_resolved_at_for_rejected_status(): void
    {
        $input = new UpdateInput(1, 'desc', 'reason', 1, 10, 20, 2, 5, 'Ivan', '+79991234567', 'ivan@test.com', 3, 4, [], []);

        $this->validator->expects($this->once())->method('validate');
        $this->categoryService->expects($this->once())->method('getById')->willReturn(new TicketCategoryEntity);
        $this->serviceService->expects($this->once())->method('getById')->willReturn(new TicketServiceEntity);

        $this->ticketService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(TicketEntity $t) => $t->getResolvedAt() !== null))
            ->willReturnCallback(fn(TicketEntity $t) => $t->setId(1))
        ;

        $this->fileService->expects($this->once())->method('storeTicketFiles');
        $this->fileService->expects($this->once())->method('storeTicketResultFiles');

        $this->command->execute($input);
    }

    public function test_execute_sets_resolved_at_when_result_provided(): void
    {
        $input = new UpdateInput(1, 'desc', 'result text', 1, 10, 20, 2, 1, 'Ivan', '+79991234567', 'ivan@test.com', 3, 4, [], []);

        $this->validator->expects($this->once())->method('validate');
        $this->categoryService->expects($this->once())->method('getById')->willReturn(new TicketCategoryEntity);
        $this->serviceService->expects($this->once())->method('getById')->willReturn(new TicketServiceEntity);

        $this->ticketService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(TicketEntity $t) => $t->getResolvedAt() !== null))
            ->willReturnCallback(fn(TicketEntity $t) => $t->setId(1))
        ;

        $this->fileService->expects($this->once())->method('storeTicketFiles');
        $this->fileService->expects($this->once())->method('storeTicketResultFiles');

        $this->command->execute($input);
    }

    public function test_execute_clears_resolved_at_for_open_status(): void
    {
        $input = new UpdateInput(1, 'desc', null, 1, 10, 20, 2, 1, 'Ivan', '+79991234567', 'ivan@test.com', 3, 4, [], []);

        $this->validator->expects($this->once())->method('validate');
        $this->categoryService->expects($this->once())->method('getById')->willReturn(new TicketCategoryEntity);
        $this->serviceService->expects($this->once())->method('getById')->willReturn(new TicketServiceEntity);

        $this->ticketService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(TicketEntity $t) => $t->getResolvedAt() === null))
            ->willReturnCallback(fn(TicketEntity $t) => $t->setId(1))
        ;

        $this->fileService->expects($this->once())->method('storeTicketFiles');
        $this->fileService->expects($this->once())->method('storeTicketResultFiles');

        $this->command->execute($input);
    }

    public function test_execute_throws_when_validator_fails(): void
    {
        $input = $this->makeInput();

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($input)
            ->willThrowException(new ValidationException(['description' => ['error']]))
        ;

        $this->expectException(ValidationException::class);
        $this->command->execute($input);
    }
}
