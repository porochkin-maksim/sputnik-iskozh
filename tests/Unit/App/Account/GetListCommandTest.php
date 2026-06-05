<?php declare(strict_types=1);

namespace Tests\Unit\App\Account;

use Core\App\Account\GetListCommand;
use Core\App\Account\ListValidator;
use Core\Domains\Account\AccountSearchResponse;
use Core\Domains\Account\AccountService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private AccountService $accountService;
    private ListValidator  $listValidator;
    private GetListCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountService = $this->createMock(AccountService::class);
        $this->listValidator  = $this->createMock(ListValidator::class);
        $this->command        = new GetListCommand(
            $this->accountService,
            $this->listValidator,
        );
    }

    public function test_execute_with_search(): void
    {
        $accounts    = new AccountSearchResponse;
        $allAccounts = new AccountSearchResponse;

        $this->listValidator->expects($this->once())
            ->method('validate')
            ->with(10, 0, 'id', 'asc')
        ;

        $this->accountService->method('search')
            ->willReturnOnConsecutiveCalls($accounts, $allAccounts)
        ;

        $result = $this->command->execute(10, 0, 'search', null, 'id', 'asc');

        $this->assertSame($accounts, $result['accounts']);
        $this->assertSame($allAccounts, $result['allAccounts']);
    }

    public function test_execute_without_search(): void
    {
        $accounts    = new AccountSearchResponse;
        $allAccounts = new AccountSearchResponse;

        $this->listValidator->method('validate');

        $this->accountService->method('search')
            ->willReturnOnConsecutiveCalls($accounts, $allAccounts)
        ;

        $result = $this->command->execute(null, null, null, null, null, null);

        $this->assertSame($accounts, $result['accounts']);
        $this->assertSame($allAccounts, $result['allAccounts']);
    }

    public function test_execute_with_account_id(): void
    {
        $accounts    = new AccountSearchResponse;
        $allAccounts = new AccountSearchResponse;

        $this->listValidator->method('validate');

        $this->accountService->method('search')
            ->willReturnOnConsecutiveCalls($accounts, $allAccounts)
        ;

        $result = $this->command->execute(null, null, null, 5, null, null);

        $this->assertSame($accounts, $result['accounts']);
        $this->assertSame($allAccounts, $result['allAccounts']);
    }

    public function test_execute_validates_before_search(): void
    {
        $this->listValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['limit' => ['error']]))
        ;

        $this->accountService->expects($this->never())->method('search');

        $this->expectException(ValidationException::class);

        $this->command->execute(0, null, null, null, null, null);
    }
}
