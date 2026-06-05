<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\UpdateInput;
use Core\App\HelpDesk\Ticket\UpdateValidator;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class UpdateValidatorTest extends TestCase
{
    private TicketCategoryService $categoryService;
    private TicketCatalogService  $serviceService;
    private AccountService        $accountService;
    private UserService           $userService;
    private UpdateValidator       $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = $this->createMock(TicketCategoryService::class);
        $this->serviceService  = $this->createMock(TicketCatalogService::class);
        $this->accountService  = $this->createMock(AccountService::class);
        $this->userService     = $this->createMock(UserService::class);

        $this->validator = new UpdateValidator(
            $this->categoryService,
            $this->serviceService,
            $this->accountService,
            $this->userService,
        );
    }

    private function makeValidInput(): UpdateInput
    {
        return new UpdateInput(
            id          : 1,
            description : 'valid description',
            result      : null,
            type        : 1,
            categoryId  : 10,
            serviceId   : 20,
            priority    : 2,
            status      : 1,
            contactName : 'Ivan',
            contactPhone: '+79991234567',
            contactEmail: 'ivan@test.com',
            userId      : 2,
            accountId   : 3,
            files       : [],
            resultFiles : [],
        );
    }

    public function test_valid_data_passes(): void
    {
        $input = $this->makeValidInput();

        $category = (new TicketCategoryEntity)->setId(10)->setIsActive(true);
        $service  = (new TicketServiceEntity)->setId(20)->setIsActive(true)->setCategoryId(10);

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
        $this->userService->expects($this->once())
            ->method('getById')
            ->with(2, true)
            ->willReturn(new UserEntity)
        ;
        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(3)
            ->willReturn(new AccountEntity)
        ;

        $this->validator->validate($input);
    }

    public function test_empty_description_throws(): void
    {
        $input = new UpdateInput(1, '', null, 1, 10, 20, 2, 1, 'Ivan', '+79991234567', 'ivan@test.com', 2, 3, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_short_description_throws(): void
    {
        $input = new UpdateInput(1, 'abc', null, 1, 10, 20, 2, 1, 'Ivan', '+79991234567', 'ivan@test.com', 2, 3, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_invalid_type_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 999, 10, 20, 2, 1, 'Ivan', '+79991234567', 'ivan@test.com', 2, 3, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_invalid_priority_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, 10, 20, 999, 1, 'Ivan', '+79991234567', 'ivan@test.com', 2, 3, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_invalid_status_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, 10, 20, 2, 999, 'Ivan', '+79991234567', 'ivan@test.com', 2, 3, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_closed_without_result_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, 10, 20, 2, 4, 'Ivan', '+79991234567', 'ivan@test.com', 2, 3, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_rejected_without_result_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, 10, 20, 2, 5, 'Ivan', '+79991234567', 'ivan@test.com', 2, 3, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_category_not_found_throws(): void
    {
        $input = $this->makeValidInput();

        $this->categoryService->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_inactive_category_throws(): void
    {
        $input    = $this->makeValidInput();
        $category = (new TicketCategoryEntity)->setId(10)->setIsActive(false);

        $this->categoryService->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($category)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_service_not_found_throws(): void
    {
        $input    = $this->makeValidInput();
        $category = (new TicketCategoryEntity)->setId(10)->setIsActive(true);

        $this->categoryService->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($category)
        ;
        $this->serviceService->expects($this->once())
            ->method('getById')
            ->with(20)
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_inactive_service_throws(): void
    {
        $input    = $this->makeValidInput();
        $category = (new TicketCategoryEntity)->setId(10)->setIsActive(true);
        $service  = (new TicketServiceEntity)->setId(20)->setIsActive(false)->setCategoryId(10);

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

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_service_not_in_category_throws(): void
    {
        $input    = $this->makeValidInput();
        $category = (new TicketCategoryEntity)->setId(10)->setIsActive(true);
        $service  = (new TicketServiceEntity)->setId(20)->setIsActive(true)->setCategoryId(99);

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

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_null_contact_name_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, null, null, 2, 1, null, '+79991234567', 'ivan@test.com', null, null, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_long_contact_name_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, null, null, 2, 1, str_repeat('x', 256), '+79991234567', 'ivan@test.com', null, null, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_long_contact_email_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, null, null, 2, 1, 'Ivan', '+79991234567', str_repeat('x', 256) . '@test.com', null, null, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_invalid_contact_email_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, null, null, 2, 1, 'Ivan', '+79991234567', 'invalid', null, null, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_long_contact_phone_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, null, null, 2, 1, 'Ivan', str_repeat('1', 21), 'ivan@test.com', null, null, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_user_not_found_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, null, null, 2, 1, 'Ivan', '+79991234567', 'ivan@test.com', 2, null, [], []);

        $this->userService->expects($this->once())
            ->method('getById')
            ->with(2, true)
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_account_not_found_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, null, null, 2, 1, 'Ivan', '+79991234567', 'ivan@test.com', null, 3, [], []);

        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(3)
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_no_contacts_throws(): void
    {
        $input = new UpdateInput(1, 'description', null, 1, null, null, 2, 1, 'Ivan', null, null, null, null, [], []);

        $this->expectException(ValidationException::class);
        $this->validator->validate($input);
    }

    public function test_validation_returns_all_errors(): void
    {
        $input = new UpdateInput(null, '', null, 999, null, null, 999, 999, null, null, null, null, null, [], []);

        try {
            $this->validator->validate($input);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('description', $e->errors);
            $this->assertArrayHasKey('type', $e->errors);
            $this->assertArrayHasKey('priority', $e->errors);
            $this->assertArrayHasKey('status', $e->errors);
            $this->assertArrayHasKey('contact_name', $e->errors);
            $this->assertArrayHasKey('contact_email', $e->errors);
            $this->assertArrayHasKey('contact_phone', $e->errors);
        }
    }
}
