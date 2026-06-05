<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\StoreValidator;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class StoreValidatorTest extends TestCase
{
    private StoreValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new StoreValidator;
    }

    public function test_non_empty_files_passes(): void
    {
        $file = new UploadedFile('test.pdf', '/tmp/test.pdf', 'application/pdf', 100, 'content');

        $this->validator->validate([$file]);

        $this->expectNotToPerformAssertions();
    }

    public function test_empty_files_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate([]);
    }
}
