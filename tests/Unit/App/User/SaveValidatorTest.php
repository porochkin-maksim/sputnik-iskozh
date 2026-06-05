<?php declare(strict_types=1);

namespace Tests\Unit\App\User;

use Core\App\User\SaveValidator;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveValidatorTest extends TestCase
{
    private UserService   $userService;
    private SaveValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->createMock(UserService::class);
        $this->validator   = new SaveValidator($this->userService);
    }

    public function test_valid_email_passes(): void
    {
        $this->userService->method('getByEmail')->willReturn(null);

        $this->validator->validate(null, 'test@example.com');

        $this->expectNotToPerformAssertions();
    }

    public function test_null_email_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, null);
    }

    public function test_empty_email_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, '');
    }

    public function test_invalid_email_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(null, 'not-an-email');
    }

    public function test_email_too_long_throws(): void
    {
        $long = str_repeat('a', 256) . '@b.com';

        $this->expectException(ValidationException::class);

        $this->validator->validate(null, $long);
    }

    public function test_duplicate_email_throws(): void
    {
        $existing = (new UserEntity)->setId(2);

        $this->userService->method('getByEmail')->willReturn($existing);

        $this->expectException(ValidationException::class);

        $this->validator->validate(null, 'test@example.com');
    }

    public function test_duplicate_email_same_user_passes(): void
    {
        $existing = (new UserEntity)->setId(1);

        $this->userService->method('getByEmail')->willReturn($existing);

        $this->validator->validate(1, 'test@example.com');

        $this->expectNotToPerformAssertions();
    }

    public function test_validation_returns_all_errors(): void
    {
        $this->userService->method('getByEmail')->willReturn(null);

        try {
            $this->validator->validate(null, '');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->errors);
        }
    }
}
