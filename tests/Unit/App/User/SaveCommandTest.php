<?php declare(strict_types=1);

namespace Tests\Unit\App\User;

use Core\App\User\SaveCommand;
use Core\App\User\SaveValidator;
use Core\Contracts\StringServiceInterface;
use Core\Domains\Access\RoleEntity;
use Core\Domains\Access\RoleService;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\ExDataEntity;
use Core\Domains\Infra\ExData\Services\ExDataService;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserFactory;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private StringServiceInterface $stringService;
    private UserFactory            $userFactory;
    private UserService            $userService;
    private AccountService         $accountService;
    private RoleService            $roleService;
    private ExDataService          $exDataService;
    private SaveValidator          $saveValidator;
    private SaveCommand            $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stringService  = $this->createMock(StringServiceInterface::class);
        $this->userFactory    = $this->createMock(UserFactory::class);
        $this->userService    = $this->createMock(UserService::class);
        $this->accountService = $this->createMock(AccountService::class);
        $this->roleService    = $this->createMock(RoleService::class);
        $this->exDataService  = $this->createMock(ExDataService::class);
        $this->saveValidator  = $this->createMock(SaveValidator::class);
        $this->command        = new SaveCommand(
            $this->stringService,
            $this->userFactory,
            $this->userService,
            $this->accountService,
            $this->roleService,
            $this->exDataService,
            $this->saveValidator,
        );
    }

    public function test_execute_creates_new_user(): void
    {
        $this->saveValidator->method('validate');

        $defaultUser = (new UserEntity)->setId(null);

        $this->stringService->method('random')->with(8)->willReturn('randomPass');
        $this->userFactory->method('makeDefault')->willReturn($defaultUser);

        $role = new RoleEntity;
        $this->roleService->method('getById')->with(2)->willReturn($role);

        $this->accountService->expects($this->never())->method('getByIds');

        $savedUser = (new UserEntity)->setId(5);
        $this->userService->method('save')->willReturn($savedUser);

        $exData = new ExDataEntity;
        $this->exDataService->method('getByTypeAndReferenceId')->willReturn($exData);

        $result = $this->command->execute(
            id                : null,
            firstName         : 'John',
            middleName        : null,
            lastName          : 'Doe',
            email             : 'john@example.com',
            phone             : null,
            roleId            : 2,
            membershipDutyInfo: null,
            membershipDate    : null,
            fractions         : [],
            ownerDates        : [],
            addPhone          : null,
            legalAddress      : null,
            postAddress       : null,
            additional        : null,
        );

        $this->assertSame($savedUser, $result);
    }

    public function test_execute_updates_existing_user(): void
    {
        $this->saveValidator->method('validate');

        $existingUser = (new UserEntity)->setId(10);

        $this->userService->expects($this->once())
            ->method('getById')
            ->with(10, true)
            ->willReturn($existingUser)
        ;

        $this->userFactory->expects($this->never())->method('makeDefault');

        $role = new RoleEntity;
        $this->roleService->method('getById')->with(3)->willReturn($role);

        $savedUser = (new UserEntity)->setId(10);
        $this->userService->method('save')->willReturn($savedUser);

        $exData = new ExDataEntity;
        $this->exDataService->method('getByTypeAndReferenceId')->willReturn($exData);

        $result = $this->command->execute(
            id                : 10,
            firstName         : 'Jane',
            middleName        : null,
            lastName          : 'Smith',
            email             : 'jane@example.com',
            phone             : null,
            roleId            : 3,
            membershipDutyInfo: null,
            membershipDate    : null,
            fractions         : [],
            ownerDates        : [],
            addPhone          : null,
            legalAddress      : null,
            postAddress       : null,
            additional        : null,
        );

        $this->assertSame($savedUser, $result);
    }

    public function test_execute_returns_null_when_user_not_found(): void
    {
        $this->saveValidator->method('validate');

        $this->userService->method('getById');

        $this->userService->expects($this->never())->method('save');

        $result = $this->command->execute(
            id                : 999,
            firstName         : null,
            middleName        : null,
            lastName          : null,
            email             : 'test@example.com',
            phone             : null,
            roleId            : 1,
            membershipDutyInfo: null,
            membershipDate    : null,
            fractions         : [],
            ownerDates        : [],
            addPhone          : null,
            legalAddress      : null,
            postAddress       : null,
            additional        : null,
        );

        $this->assertNull($result);
    }

    public function test_execute_validates_before_any_action(): void
    {
        $this->saveValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['email' => ['error']]))
        ;

        $this->userService->expects($this->never())->method('getById');
        $this->userFactory->expects($this->never())->method('makeDefault');

        $this->expectException(ValidationException::class);

        $this->command->execute(
            id                : null,
            firstName         : null,
            middleName        : null,
            lastName          : null,
            email             : 'bad',
            phone             : null,
            roleId            : 1,
            membershipDutyInfo: null,
            membershipDate    : null,
            fractions         : [],
            ownerDates        : [],
            addPhone          : null,
            legalAddress      : null,
            postAddress       : null,
            additional        : null,
        );
    }

    public function test_execute_processes_fractions(): void
    {
        $this->saveValidator->method('validate');

        $defaultUser = (new UserEntity)->setId(null);
        $this->userFactory->method('makeDefault')->willReturn($defaultUser);
        $this->stringService->method('random')->willReturn('pass');

        $role = new RoleEntity;
        $this->roleService->method('getById')->willReturn($role);

        $account1 = (new AccountEntity)->setId(1);
        $account2 = (new AccountEntity)->setId(2);
        $accounts = new AccountCollection([$account1, $account2]);

        $this->accountService->method('getByIds')
            ->with([1, 2])
            ->willReturn($accounts)
        ;

        $savedUser = (new UserEntity)->setId(5);
        $this->userService->method('save')->willReturn($savedUser);

        $exData = new ExDataEntity;
        $this->exDataService->method('getByTypeAndReferenceId')->willReturn($exData);

        $result = $this->command->execute(
            id                : null,
            firstName         : null,
            middleName        : null,
            lastName          : null,
            email             : 'test@test.com',
            phone             : null,
            roleId            : 1,
            membershipDutyInfo: null,
            membershipDate    : null,
            fractions         : [1 => '0.5', 2 => '0.3'],
            ownerDates        : [1 => '2024-01-01', 2 => null],
            addPhone          : null,
            legalAddress      : null,
            postAddress       : null,
            additional        : null,
        );

        $this->assertSame($savedUser, $result);
    }

    public function test_execute_updates_ex_data(): void
    {
        $this->saveValidator->method('validate');

        $defaultUser = (new UserEntity)->setId(null);
        $this->userFactory->method('makeDefault')->willReturn($defaultUser);
        $this->stringService->method('random')->willReturn('pass');

        $role = new RoleEntity;
        $this->roleService->method('getById')->willReturn($role);

        $savedUser = (new UserEntity)->setId(5);
        $this->userService->method('save')->willReturn($savedUser);

        $exData = new ExDataEntity;
        $this->exDataService->method('getByTypeAndReferenceId')
            ->with(ExDataTypeEnum::USER, 5)
            ->willReturn($exData)
        ;

        $this->exDataService->expects($this->once())->method('save')->with($exData);

        $this->command->execute(
            id                : null,
            firstName         : null,
            middleName        : null,
            lastName          : null,
            email             : 'test@test.com',
            phone             : null,
            roleId            : 1,
            membershipDutyInfo: null,
            membershipDate    : null,
            fractions         : [],
            ownerDates        : [],
            addPhone          : '81234567890',
            legalAddress      : 'Address 1',
            postAddress       : null,
            additional        : 'Some info',
        );
    }
}
