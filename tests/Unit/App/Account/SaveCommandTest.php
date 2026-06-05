<?php declare(strict_types=1);

namespace Tests\Unit\App\Account;

use Core\App\Account\SaveCommand;
use Core\App\Account\SaveValidator;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountFactory;
use Core\Domains\Account\AccountService;
use Core\Domains\Infra\ExData\ExDataEntity;
use Core\Domains\Infra\ExData\Services\ExDataService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private AccountFactory $accountFactory;
    private AccountService $accountService;
    private ExDataService  $exDataService;
    private SaveValidator  $saveValidator;
    private SaveCommand    $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountFactory = $this->createMock(AccountFactory::class);
        $this->accountService = $this->createMock(AccountService::class);
        $this->exDataService  = $this->createMock(ExDataService::class);
        $this->saveValidator  = $this->createMock(SaveValidator::class);
        $this->command        = new SaveCommand(
            $this->accountFactory,
            $this->accountService,
            $this->exDataService,
            $this->saveValidator,
        );
    }

    public function test_execute_creates_new_account(): void
    {
        $this->saveValidator->method('validate');

        $defaultAccount = new AccountEntity;
        $this->accountFactory->method('makeDefault')->willReturn($defaultAccount);

        $savedAccount = (new AccountEntity)->setId(1);
        $this->accountService->method('save')->willReturn($savedAccount);

        $exData = new ExDataEntity;
        $this->exDataService->method('getByTypeAndReferenceId')->willReturn($exData);

        $this->accountService->method('getById')->with(1)->willReturn($savedAccount);

        $result = $this->command->execute(
            id            : null,
            number        : '125',
            isInvoicing   : true,
            size          : 10,
            cadastreNumber: null,
        );

        $this->assertSame($savedAccount, $result);
    }

    public function test_execute_updates_existing_account(): void
    {
        $this->saveValidator->method('validate');

        $existingAccount = (new AccountEntity)->setId(5);

        $savedAccount = (new AccountEntity)->setId(5);

        $this->accountService->expects($this->exactly(2))
            ->method('getById')
            ->willReturnOnConsecutiveCalls($existingAccount, $savedAccount)
        ;

        $this->accountService->method('save')->willReturn($savedAccount);

        $exData = new ExDataEntity;
        $this->exDataService->method('getByTypeAndReferenceId')->willReturn($exData);

        $result = $this->command->execute(
            id            : 5,
            number        : '125',
            isInvoicing   : false,
            size          : 15,
            cadastreNumber: null,
        );

        $this->assertSame($savedAccount, $result);
    }

    public function test_execute_returns_null_when_account_not_found(): void
    {
        $this->saveValidator->method('validate');

        $this->accountService->method('getById')->willReturn(null);

        $this->accountService->expects($this->never())->method('save');

        $result = $this->command->execute(
            id            : 999,
            number        : '999',
            isInvoicing   : false,
            size          : 10,
            cadastreNumber: null,
        );

        $this->assertNull($result);
    }

    public function test_execute_validates_before_any_action(): void
    {
        $this->saveValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['number' => ['error']]))
        ;

        $this->accountFactory->expects($this->never())->method('makeDefault');
        $this->accountService->expects($this->never())->method('getById');

        $this->expectException(ValidationException::class);

        $this->command->execute(
            id            : null,
            number        : null,
            isInvoicing   : false,
            size          : null,
            cadastreNumber: null,
        );
    }

    public function test_execute_with_cadastre_number(): void
    {
        $this->saveValidator->method('validate');

        $defaultAccount = new AccountEntity;
        $this->accountFactory->method('makeDefault')->willReturn($defaultAccount);

        $savedAccount = (new AccountEntity)->setId(2);
        $this->accountService->method('save')->willReturn($savedAccount);

        $exData = new ExDataEntity;
        $this->exDataService->method('getByTypeAndReferenceId')->willReturn($exData);

        $this->exDataService->expects($this->once())->method('save')->with($exData);

        $this->accountService->method('getById')->with(2)->willReturn($savedAccount);

        $result = $this->command->execute(
            id            : null,
            number        : '200',
            isInvoicing   : true,
            size          : 20,
            cadastreNumber: '77:01:0001020:5',
        );

        $this->assertSame($savedAccount, $result);
    }
}
