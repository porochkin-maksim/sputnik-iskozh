<?php declare(strict_types=1);

namespace Tests\Unit\App\News;

use Core\App\News\SaveFileValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveFileValidatorTest extends TestCase
{
    private SaveFileValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new SaveFileValidator;
    }

    public function test_valid_name_passes(): void
    {
        $this->validator->validate('document.pdf');

        $this->expectNotToPerformAssertions();
    }

    public function test_null_name_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null);
    }

    public function test_empty_name_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('');
    }

    public function test_short_name_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('ab');
    }
}
