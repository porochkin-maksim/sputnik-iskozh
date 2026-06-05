<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\LinkCounterHistoryValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class LinkCounterHistoryValidatorTest extends TestCase
{
    private LinkCounterHistoryValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new LinkCounterHistoryValidator;
    }

    public function test_valid_data_passes(): void
    {
        $this->validator->validate(1, 2);
        $this->expectNotToPerformAssertions();
    }

    public function test_null_id_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(null, 2);
    }

    public function test_null_counter_id_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, null);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(null, null);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('id', $e->errors);
            $this->assertArrayHasKey('counter_id', $e->errors);
        }
    }
}
