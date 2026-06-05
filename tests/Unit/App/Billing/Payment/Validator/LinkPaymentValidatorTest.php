<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Payment\Validator;

use Core\App\Billing\Payment\Validator\LinkPaymentValidator;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class LinkPaymentValidatorTest extends TestCase
{
    private AccountService       $accountService;
    private LinkPaymentValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountService = $this->createMock(AccountService::class);
        $this->validator      = new LinkPaymentValidator($this->accountService);
    }

    public function test_valid_data_passes(): void
    {
        $account = new AccountEntity;
        $account->setId(10);

        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($account)
        ;

        $this->validator->validate(1000.0, 10);
    }

    public function test_null_cost_throws(): void
    {
        $account = new AccountEntity;
        $account->setId(10);

        $this->accountService->method('getById')->willReturn($account);

        $this->expectException(ValidationException::class);
        $this->validator->validate(null, 10);
    }

    public function test_negative_cost_throws(): void
    {
        $account = new AccountEntity;
        $account->setId(10);

        $this->accountService->method('getById')->willReturn($account);

        $this->expectException(ValidationException::class);
        $this->validator->validate(-1.0, 10);
    }

    public function test_null_account_id_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1000.0, null);
    }

    public function test_non_existent_account_throws(): void
    {
        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate(1000.0, 999);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(null, null);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('cost', $e->errors);
            $this->assertArrayHasKey('account_id', $e->errors);
        }
    }
}
