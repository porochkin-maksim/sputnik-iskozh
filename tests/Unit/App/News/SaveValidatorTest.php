<?php declare(strict_types=1);

namespace Tests\Unit\App\News;

use Core\App\News\SaveValidator;
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
        $this->validator->validate('Title', 'Desc', 1);

        $this->expectNotToPerformAssertions();
    }

    public function test_empty_title_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('', null, null);
    }

    public function test_title_too_long_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(str_repeat('a', 256), null, null);
    }

    public function test_description_too_long_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('Title', str_repeat('a', 256), null);
    }

    public function test_null_category_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('Title', null, null);
    }

    public function test_invalid_category_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('Title', null, 999);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate('', null, null);
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('title', $e->errors);
            $this->assertArrayHasKey('category', $e->errors);
        }
    }
}
