<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\GetListValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class GetListValidatorTest extends TestCase
{
    private GetListValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new GetListValidator;
    }

    public function test_null_params_passes(): void
    {
        $this->validator->validate(null, null);

        $this->expectNotToPerformAssertions();
    }

    public function test_valid_limit_passes(): void
    {
        $this->validator->validate(10, null);

        $this->expectNotToPerformAssertions();
    }

    public function test_zero_limit_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(0, null);
    }

    public function test_negative_limit_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(-1, null);
    }

    public function test_invalid_sort_field_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, 'invalid_field');
    }

    public function test_valid_sort_fields_pass(): void
    {
        $this->validator->validate(null, 'name');

        $this->expectNotToPerformAssertions();
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(0, 'bad');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('limit', $e->errors);
            $this->assertArrayHasKey('sort_by', $e->errors);
        }
    }
}
