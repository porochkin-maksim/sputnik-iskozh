<?php declare(strict_types=1);

namespace Tests\Unit\App\Account;

use Core\App\Account\SaveValidator;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveValidatorTest extends TestCase
{
    private AccountService $accountService;
    private SaveValidator  $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountService = $this->createMock(AccountService::class);
        $this->validator      = new SaveValidator($this->accountService);
    }

    public function test_valid_params_passes(): void
    {
        $this->accountService->method('findByNumber')->willReturn(null);

        $this->validator->validate(null, '125', 10);

        $this->expectNotToPerformAssertions();
    }

    public function test_null_number_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, null, 10);
    }

    public function test_empty_number_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, '', 10);
    }

    public function test_duplicate_number_throws(): void
    {
        $existing = (new AccountEntity)->setId(2);

        $this->accountService->method('findByNumber')->willReturn($existing);

        $this->expectException(ValidationException::class);

        $this->validator->validate(null, '125', 10);
    }

    public function test_duplicate_number_same_account_passes(): void
    {
        $existing = (new AccountEntity)->setId(5);

        $this->accountService->method('findByNumber')->willReturn($existing);

        $this->validator->validate(5, '125', 10);

        $this->expectNotToPerformAssertions();
    }

    public function test_null_size_throws(): void
    {
        $this->accountService->method('findByNumber')->willReturn(null);

        $this->expectException(ValidationException::class);

        $this->validator->validate(null, '125', null);
    }

    public function test_negative_size_throws(): void
    {
        $this->accountService->method('findByNumber')->willReturn(null);

        $this->expectException(ValidationException::class);

        $this->validator->validate(null, '125', -1);
    }

    public function test_validation_returns_all_errors(): void
    {
        $this->accountService->method('findByNumber')->willReturn(null);

        try {
            $this->validator->validate(null, null, null);
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('number', $e->errors);
            $this->assertArrayHasKey('size', $e->errors);
        }
    }
}
