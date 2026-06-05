<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\CreateInput;
use Core\App\HelpDesk\Ticket\CreateValidator;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class CreateValidatorTest extends TestCase
{
    private TicketCategoryService $categoryService;
    private TicketCatalogService  $serviceService;
    private AccountService        $accountService;
    private UserService           $userService;
    private CreateValidator       $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = $this->createMock(TicketCategoryService::class);
        $this->serviceService  = $this->createMock(TicketCatalogService::class);
        $this->accountService  = $this->createMock(AccountService::class);
        $this->userService     = $this->createMock(UserService::class);

        $this->validator = new CreateValidator(
            $this->categoryService,
            $this->serviceService,
            $this->accountService,
            $this->userService,
        );
    }

    private function makeValidInput(): CreateInput
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

    public function test_valid_data_passes(): void
    {
        $input = $this->makeValidInput();

        $category = (new TicketCategoryEntity)->setId(10)->setIsActive(true);
        $service  = (new TicketServiceEntity)->setId(20)->setIsActive(true);

        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new AccountEntity)
        ;
        $this->userService->expects($this->once())
            ->method('getById')
            ->with(2, true)
            ->willReturn(new UserEntity)
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

        $this->validator->validate($input);
    }

    public function test_empty_type_code_throws(): void
    {
        $input = new CreateInput('', 'electric', 'repair', 'desc', 'Ivan', 'ivan@test.com', '+79991234567', 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_empty_category_code_throws(): void
    {
        $input = new CreateInput('incident', '', 'repair', 'desc', 'Ivan', 'ivan@test.com', '+79991234567', 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_empty_service_code_throws(): void
    {
        $input = new CreateInput('incident', 'electric', '', 'desc', 'Ivan', 'ivan@test.com', '+79991234567', 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_null_account_id_throws(): void
    {
        $input = new CreateInput('incident', 'electric', 'repair', 'desc', 'Ivan', 'ivan@test.com', '+79991234567', null, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_account_not_found_throws(): void
    {
        $input = $this->makeValidInput();

        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_empty_description_throws(): void
    {
        $input = new CreateInput('incident', 'electric', 'repair', '', 'Ivan', 'ivan@test.com', '+79991234567', 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_short_description_throws(): void
    {
        $input = new CreateInput('incident', 'electric', 'repair', 'abc', 'Ivan', 'ivan@test.com', '+79991234567', 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_long_contact_name_throws(): void
    {
        $input = new CreateInput('incident', 'electric', 'repair', 'description', str_repeat('x', 256), 'ivan@test.com', '+79991234567', 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_long_contact_email_throws(): void
    {
        $input = new CreateInput('incident', 'electric', 'repair', 'description', 'Ivan', str_repeat('x', 256) . '@test.com', '+79991234567', 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_invalid_contact_email_throws(): void
    {
        $input = new CreateInput('incident', 'electric', 'repair', 'description', 'Ivan', 'invalid-email', '+79991234567', 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_long_contact_phone_throws(): void
    {
        $input = new CreateInput('incident', 'electric', 'repair', 'description', 'Ivan', 'ivan@test.com', str_repeat('1', 21), 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_user_not_found_throws(): void
    {
        $input = $this->makeValidInput();

        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new AccountEntity)
        ;
        $this->userService->expects($this->once())
            ->method('getById')
            ->with(2, true)
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_too_many_files_throws(): void
    {
        $files = [];
        for ($i = 0; $i < 6; $i++) {
            $files[] = new UploadedFile("file{$i}.pdf", "/tmp/file{$i}.pdf", 'application/pdf', 1024, '');
        }
        $input = new CreateInput('incident', 'electric', 'repair', 'description', 'Ivan', 'ivan@test.com', '+79991234567', 1, 2, $files);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_file_too_large_throws(): void
    {
        $files = [new UploadedFile('big.pdf', '/tmp/big.pdf', 'application/pdf', 21 * 1024 * 1024, '')];
        $input = new CreateInput('incident', 'electric', 'repair', 'description', 'Ivan', 'ivan@test.com', '+79991234567', 1, 2, $files);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_invalid_type_code_throws(): void
    {
        $input = new CreateInput('invalid', 'electric', 'repair', 'description', 'Ivan', 'ivan@test.com', '+79991234567', 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_category_not_found_throws(): void
    {
        $input = $this->makeValidInput();

        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new AccountEntity)
        ;
        $this->userService->expects($this->once())
            ->method('getById')
            ->with(2, true)
            ->willReturn(new UserEntity)
        ;
        $this->categoryService->expects($this->once())
            ->method('findByTypeAndCode')
            ->with(TicketTypeEnum::INCIDENT, 'electric')
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_inactive_category_throws(): void
    {
        $input    = $this->makeValidInput();
        $category = (new TicketCategoryEntity)->setId(10)->setIsActive(false);

        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new AccountEntity)
        ;
        $this->userService->expects($this->once())
            ->method('getById')
            ->with(2, true)
            ->willReturn(new UserEntity)
        ;
        $this->categoryService->expects($this->once())
            ->method('findByTypeAndCode')
            ->with(TicketTypeEnum::INCIDENT, 'electric')
            ->willReturn($category)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_service_not_found_throws(): void
    {
        $input    = $this->makeValidInput();
        $category = (new TicketCategoryEntity)->setId(10)->setIsActive(true);

        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new AccountEntity)
        ;
        $this->userService->expects($this->once())
            ->method('getById')
            ->with(2, true)
            ->willReturn(new UserEntity)
        ;
        $this->categoryService->expects($this->once())
            ->method('findByTypeAndCode')
            ->with(TicketTypeEnum::INCIDENT, 'electric')
            ->willReturn($category)
        ;
        $this->serviceService->expects($this->once())
            ->method('findByCategoryIdAndCode')
            ->with(10, 'repair')
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_inactive_service_throws(): void
    {
        $input    = $this->makeValidInput();
        $category = (new TicketCategoryEntity)->setId(10)->setIsActive(true);
        $service  = (new TicketServiceEntity)->setId(20)->setIsActive(false);

        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new AccountEntity)
        ;
        $this->userService->expects($this->once())
            ->method('getById')
            ->with(2, true)
            ->willReturn(new UserEntity)
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

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_no_contacts_throws(): void
    {
        $input = new CreateInput('incident', 'electric', 'repair', 'description', null, null, null, 1, 2, []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_validation_returns_all_errors(): void
    {
        $input = new CreateInput('', '', '', '', null, null, null, null, null, []);

        try {
            $this->validator->validate($input);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('typeCode', $e->errors);
            $this->assertArrayHasKey('categoryCode', $e->errors);
            $this->assertArrayHasKey('serviceCode', $e->errors);
            $this->assertArrayHasKey('accountId', $e->errors);
            $this->assertArrayHasKey('description', $e->errors);
            $this->assertArrayHasKey('contact', $e->errors);
        }
    }
}
