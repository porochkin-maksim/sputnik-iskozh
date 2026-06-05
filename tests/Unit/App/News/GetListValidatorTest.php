<?php declare(strict_types=1);

namespace Tests\Unit\App\News;

use Core\App\News\GetListValidator;
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
        $this->validator->validate(null, null, null);

        $this->expectNotToPerformAssertions();
    }

    public function test_valid_params_passes(): void
    {
        $this->validator->validate(10, 0, 'search');

        $this->expectNotToPerformAssertions();
    }

    public function test_zero_limit_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(0, null, null);
    }

    public function test_negative_offset_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, -1, null);
    }

    public function test_search_too_long_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, null, str_repeat('a', 256));
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(0, -1, str_repeat('a', 256));
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('limit', $e->errors);
            $this->assertArrayHasKey('offset', $e->errors);
            $this->assertArrayHasKey('search', $e->errors);
        }
    }
}
