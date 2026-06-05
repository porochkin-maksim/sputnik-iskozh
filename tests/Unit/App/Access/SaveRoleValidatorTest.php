<?php declare(strict_types=1);

namespace Tests\Unit\App\Access;

use Core\App\Access\SaveRoleValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveRoleValidatorTest extends TestCase
{
    private SaveRoleValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new SaveRoleValidator;
    }

    public function test_valid_data_passes(): void
    {
        $this->validator->validate('Admin', [1, 2, 3]);

        $this->expectNotToPerformAssertions();
    }

    public function test_null_name_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, [1]);
    }

    public function test_empty_name_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('', [1]);
    }

    public function test_empty_permissions_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('Admin', []);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate('', []);
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('name', $e->errors);
            $this->assertArrayHasKey('permissions', $e->errors);
        }
    }
}
