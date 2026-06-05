<?php declare(strict_types=1);

namespace Tests\Unit\App\Folders;

use Core\App\Folders\GetListValidator;
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

    public function test_null_limit_passes(): void
    {
        $this->validator->validate(null);

        $this->expectNotToPerformAssertions();
    }

    public function test_valid_limit_passes(): void
    {
        $this->validator->validate(10);

        $this->expectNotToPerformAssertions();
    }

    public function test_zero_limit_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(0);
    }

    public function test_negative_limit_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(-1);
    }
}
