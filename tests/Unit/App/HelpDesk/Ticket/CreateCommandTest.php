<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\CreateCommand;
use Core\App\HelpDesk\Ticket\CreateInput;
use Core\App\HelpDesk\Ticket\CreateValidator;
use Core\Contracts\EventDispatcherInterface;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Events\TicketCreated;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Services\FileService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class CreateCommandTest extends TestCase
{
    private TicketService            $ticketService;
    private TicketCategoryService    $categoryService;
    private TicketCatalogService     $serviceService;
    private FileService              $fileService;
    private CreateValidator          $validator;
    private EventDispatcherInterface $eventDispatcher;
    private CreateCommand            $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketService   = $this->createMock(TicketService::class);
        $this->categoryService = $this->createMock(TicketCategoryService::class);
        $this->serviceService  = $this->createMock(TicketCatalogService::class);
        $this->fileService     = $this->createMock(FileService::class);
        $this->validator       = $this->createMock(CreateValidator::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->command = new CreateCommand(
            $this->ticketService,
            $this->categoryService,
            $this->serviceService,
            $this->fileService,
            $this->validator,
            $this->eventDispatcher,
        );
    }

    private function makeInput(): CreateInput
    {
        return new CreateInput(
            typeCode    : 'incident',
            categoryCode: 'electric',
            serviceCode : 'repair',
            description : 'test description',
            contactName : 'Ivan',
            contactEmail: 'ivan@test.com',
            contactPhone: '+79991234567',
            accountId   : 1,
            userId      : 2,
            files       : [],
        );
    }

    public function test_execute_creates_ticket_on_happy_path(): void
    {
        $input    = $this->makeInput();
        $files    = [new UploadedFile('doc.pdf', '/tmp/doc.pdf', 'application/pdf', 1024, '')];
        $input    = new CreateInput('incident', 'electric', 'repair', 'desc', 'Ivan', null, null, 1, null, $files);
        $category = (new TicketCategoryEntity)->setId(10);
        $service  = (new TicketServiceEntity)->setId(20);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($input)
        ;

        $this->categoryService->expects($this->once())
            ->method('findByTypeAndCode')
            ->with(TicketTypeEnum::INCIDENT, 'electric')
            ->willReturn($category)
        ;

        $this->serviceService->expects($this->once())
            ->method('findByCategoryIdAndCode')
            ->with(10, 'repair')
            ->willReturn($service)
        ;

        $this->ticketService->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(TicketEntity::class))
            ->willReturnCallback(fn(TicketEntity $t) => $t->setId(50))
        ;

        $this->fileService->expects($this->once())
            ->method('storeTicketFiles')
            ->with($files, 50)
        ;

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(TicketCreated::class))
        ;

        $result = $this->command->execute($input);

        $this->assertInstanceOf(TicketEntity::class, $result);
        $this->assertSame(50, $result->getId());
        $this->assertSame(TicketTypeEnum::INCIDENT, $result->getType());
        $this->assertSame(TicketStatusEnum::NEW, $result->getStatus());
        $this->assertSame(TicketPriorityEnum::MEDIUM, $result->getPriority());
        $this->assertSame(10, $result->getCategoryId());
        $this->assertSame(20, $result->getServiceId());
    }

    public function test_execute_throws_when_validator_fails(): void
    {
        $input = $this->makeInput();

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($input)
            ->willThrowException(new ValidationException(['typeCode' => ['error']]))
        ;

        $this->expectException(ValidationException::class);
        $this->command->execute($input);
    }

    public function test_execute_sets_account_id_when_provided(): void
    {
        $input    = new CreateInput('incident', 'electric', 'repair', 'desc', null, 'ivan@test.com', null, 5, 3, []);
        $category = (new TicketCategoryEntity)->setId(10);
        $service  = (new TicketServiceEntity)->setId(20);

        $this->validator->expects($this->once())->method('validate');
        $this->categoryService->expects($this->once())->method('findByTypeAndCode')->willReturn($category);
        $this->serviceService->expects($this->once())->method('findByCategoryIdAndCode')->willReturn($service);

        $this->ticketService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(TicketEntity $t) => $t->getAccountId() === 5 && $t->getUserId() === 3))
            ->willReturnCallback(fn(TicketEntity $t) => $t->setId(1))
        ;

        $this->fileService->expects($this->once())->method('storeTicketFiles');
        $this->eventDispatcher->expects($this->once())->method('dispatch');

        $this->command->execute($input);
    }
}
