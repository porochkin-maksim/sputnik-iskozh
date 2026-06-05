<?php declare(strict_types=1);

namespace Tests\Unit\App\Folders;

use Core\App\Folders\SaveValidator;
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

    public function test_valid_name_passes(): void
    {
        $this->validator->validate('Documents');

        $this->expectNotToPerformAssertions();
    }

    public function test_empty_name_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('');
    }

    public function test_long_name_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(str_repeat('a', 256));
    }
}
