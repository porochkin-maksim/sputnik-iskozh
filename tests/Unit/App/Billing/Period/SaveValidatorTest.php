<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Period;

use Carbon\Carbon;
use Core\App\Billing\Period\SaveValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveValidatorTest extends TestCase
{
    private SaveValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new SaveValidator;
    }

    public function test_valid_data_passes(): void
    {
        $this->validator->validate('2025', Carbon::parse('2025-01-01'), Carbon::parse('2025-12-31'));
        $this->expectNotToPerformAssertions();
    }

    public function test_null_name_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(null, Carbon::parse('2025-01-01'), Carbon::parse('2025-12-31'));
    }

    public function test_empty_name_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('', Carbon::parse('2025-01-01'), Carbon::parse('2025-12-31'));
    }

    public function test_null_start_at_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('2025', null, Carbon::parse('2025-12-31'));
    }

    public function test_null_end_at_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('2025', Carbon::parse('2025-01-01'), null);
    }

    public function test_start_after_end_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('2025', Carbon::parse('2025-12-31'), Carbon::parse('2025-01-01'));
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(null, null, null);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('name', $e->errors);
            $this->assertArrayHasKey('start_at', $e->errors);
            $this->assertArrayHasKey('end_at', $e->errors);
        }
    }
}
