<?php declare(strict_types=1);

namespace Tests\Unit\App\User;

use Core\App\User\PasswordPolicyValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class PasswordPolicyValidatorTest extends TestCase
{
    private PasswordPolicyValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new PasswordPolicyValidator;
    }

    public function test_valid_password(): void
    {
        $this->validator->validate('Password1');

        $this->expectNotToPerformAssertions();
    }

    public function test_valid_complex_password(): void
    {
        $this->validator->validate('MyStr0ng!Pass');

        $this->expectNotToPerformAssertions();
    }

    public function test_empty_password_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('');
    }

    public function test_null_password_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(null);
    }

    public function test_short_password_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('Ab1');
    }

    public function test_no_lowercase_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('PASSWORD1');
    }

    public function test_no_uppercase_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('password1');
    }

    public function test_no_digit_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('Password');
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate('');
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('password', $e->errors);
        }
    }
}
