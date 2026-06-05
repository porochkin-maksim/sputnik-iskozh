<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\CreatePublicCounterHistoryValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class CreatePublicCounterHistoryValidatorTest extends TestCase
{
    private CreatePublicCounterHistoryValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new CreatePublicCounterHistoryValidator;
    }

    public function test_valid_data_passes(): void
    {
        $this->validator->validate('15/1', '12345', 100);
        $this->expectNotToPerformAssertions();
    }

    public function test_null_counter_number_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('15', null, 100);
    }

    public function test_empty_counter_number_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('15', '', 100);
    }

    public function test_empty_account_number_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('', '12345', 100);
    }

    public function test_negative_value_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('15', '12345', -1);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate('', null, -1);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('counter', $e->errors);
            $this->assertArrayHasKey('account', $e->errors);
            $this->assertArrayHasKey('value', $e->errors);
        }
    }
}
