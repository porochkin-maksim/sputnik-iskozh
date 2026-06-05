<?php declare(strict_types=1);

namespace Tests\Unit\App\Account;

use Core\App\Account\ListValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class ListValidatorTest extends TestCase
{
    private ListValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ListValidator;
    }

    public function test_null_params_passes(): void
    {
        $this->validator->validate(null, null, null, null);

        $this->expectNotToPerformAssertions();
    }

    public function test_valid_params_passes(): void
    {
        $this->validator->validate(10, 0, 'id', 'asc');

        $this->expectNotToPerformAssertions();
    }

    public function test_limit_too_low(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(0, null, null, null);
    }

    public function test_limit_too_high(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(1001, null, null, null);
    }

    public function test_negative_offset(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, -1, null, null);
    }

    public function test_invalid_sort_field(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, null, 'invalid_field', null);
    }

    public function test_invalid_sort_order(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, null, 'id', 'invalid');
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(0, -1, 'bad', 'wrong');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('limit', $e->errors);
            $this->assertArrayHasKey('skip', $e->errors);
            $this->assertArrayHasKey('sort_field', $e->errors);
            $this->assertArrayHasKey('sort_order', $e->errors);
        }
    }
}
