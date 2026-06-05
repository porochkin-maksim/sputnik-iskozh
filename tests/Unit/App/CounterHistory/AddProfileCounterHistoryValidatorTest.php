<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\AddProfileCounterHistoryValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class AddProfileCounterHistoryValidatorTest extends TestCase
{
    private AddProfileCounterHistoryValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new AddProfileCounterHistoryValidator;
    }

    public function test_valid_data_passes(): void
    {
        $this->validator->validate(1, 150, 'fake-file');
        $this->expectNotToPerformAssertions();
    }

    public function test_zero_value_passes(): void
    {
        $this->validator->validate(1, 0, 'fake-file');
        $this->expectNotToPerformAssertions();
    }

    public function test_null_counter_id_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(null, 150, 'fake-file');
    }

    public function test_null_value_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, null, 'fake-file');
    }

    public function test_empty_value_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, '', 'fake-file');
    }

    public function test_negative_value_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, -5, 'fake-file');
    }

    public function test_null_file_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, 150, null);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(null, null, null);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('counter_id', $e->errors);
            $this->assertArrayHasKey('value', $e->errors);
            $this->assertArrayHasKey('file', $e->errors);
        }
    }
}
