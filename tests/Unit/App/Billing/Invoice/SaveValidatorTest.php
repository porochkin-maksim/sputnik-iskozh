<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\SaveValidator;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveValidatorTest extends TestCase
{
    private PeriodService  $periodService;
    private AccountService $accountService;
    private SaveValidator  $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->periodService  = $this->createMock(PeriodService::class);
        $this->accountService = $this->createMock(AccountService::class);

        $this->validator = new SaveValidator(
            $this->periodService,
            $this->accountService,
        );
    }

    public function test_valid_data_passes(): void
    {
        $this->periodService->method('getById')->with(1)->willReturn(new PeriodEntity);
        $this->accountService->method('getById')->with(2)->willReturn(new AccountEntity);

        $this->validator->validate(1, 2, 1, 'test');
    }

    public function test_null_period_id_throws(): void
    {
        $this->accountService->method('getById')->with(2)->willReturn(new AccountEntity);

        $this->expectException(ValidationException::class);
        $this->validator->validate(null, 2, 1, 'test');
    }

    public function test_period_not_found_throws(): void
    {
        $this->periodService->method('getById')->with(999)->willReturn(null);
        $this->accountService->method('getById')->with(2)->willReturn(new AccountEntity);

        $this->expectException(ValidationException::class);
        $this->validator->validate(999, 2, 1, 'test');
    }

    public function test_null_account_id_throws(): void
    {
        $this->periodService->method('getById')->with(1)->willReturn(new PeriodEntity);

        $this->expectException(ValidationException::class);
        $this->validator->validate(1, null, 1, 'test');
    }

    public function test_account_not_found_throws(): void
    {
        $this->periodService->method('getById')->with(1)->willReturn(new PeriodEntity);
        $this->accountService->method('getById')->with(999)->willReturn(null);

        $this->expectException(ValidationException::class);
        $this->validator->validate(1, 999, 1, 'test');
    }

    public function test_null_type_throws(): void
    {
        $this->periodService->method('getById')->with(1)->willReturn(new PeriodEntity);
        $this->accountService->method('getById')->with(2)->willReturn(new AccountEntity);

        $this->expectException(ValidationException::class);
        $this->validator->validate(1, 2, null, 'test');
    }

    public function test_invalid_type_throws(): void
    {
        $this->periodService->method('getById')->with(1)->willReturn(new PeriodEntity);
        $this->accountService->method('getById')->with(2)->willReturn(new AccountEntity);

        $this->expectException(ValidationException::class);
        $this->validator->validate(1, 2, 999, 'test');
    }

    public function test_name_too_long_throws(): void
    {
        $this->periodService->method('getById')->with(1)->willReturn(new PeriodEntity);
        $this->accountService->method('getById')->with(2)->willReturn(new AccountEntity);

        $this->expectException(ValidationException::class);
        $this->validator->validate(1, 2, 1, str_repeat('x', 192));
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(null, null, null, str_repeat('x', 192));
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('period_id', $e->errors);
            $this->assertArrayHasKey('account_id', $e->errors);
            $this->assertArrayHasKey('type', $e->errors);
            $this->assertArrayHasKey('name', $e->errors);
        }
    }
}
