<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\AddAdminCounterHistoryValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class AddAdminCounterHistoryValidatorTest extends TestCase
{
    private AddAdminCounterHistoryValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new AddAdminCounterHistoryValidator;
    }

    public function test_valid_data_passes(): void
    {
        $this->validator->validate(1, 100);
        $this->expectNotToPerformAssertions();
    }

    public function test_zero_value_passes(): void
    {
        $this->validator->validate(1, 0);
        $this->expectNotToPerformAssertions();
    }

    public function test_null_counter_id_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(null, 100);
    }

    public function test_null_value_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, null);
    }

    public function test_empty_value_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, '');
    }

    public function test_negative_value_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, -1);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(null, null);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('counter_id', $e->errors);
            $this->assertArrayHasKey('value', $e->errors);
        }
    }
}
