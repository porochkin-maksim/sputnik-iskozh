<?php declare(strict_types=1);

namespace Tests\Unit\App\User;

use Core\App\User\PasswordPolicyValidator;
use Core\App\User\SetPasswordByTokenValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SetPasswordByTokenValidatorTest extends TestCase
{
    private PasswordPolicyValidator     $passwordPolicyValidator;
    private SetPasswordByTokenValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->passwordPolicyValidator = $this->createMock(PasswordPolicyValidator::class);
        $this->validator               = new SetPasswordByTokenValidator($this->passwordPolicyValidator);
    }

    public function test_matching_passwords_passes(): void
    {
        $this->passwordPolicyValidator->method('validate')->willReturnCallback(fn() => null);

        $this->validator->validate('Valid1Pass', 'Valid1Pass');

        $this->expectNotToPerformAssertions();
    }

    public function test_non_matching_passwords_throws(): void
    {
        $this->passwordPolicyValidator->method('validate')->willReturnCallback(fn() => null);

        $this->expectException(ValidationException::class);

        $this->validator->validate('Valid1Pass', 'Different1Pass');
    }

    public function test_delegates_to_password_policy(): void
    {
        $this->passwordPolicyValidator->expects($this->once())
            ->method('validate')
            ->with('weak')
            ->willThrowException(new ValidationException(['password' => ['policy error']]))
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate('weak', 'weak');
    }

    public function test_merges_policy_and_match_errors(): void
    {
        $this->passwordPolicyValidator->method('validate')
            ->willThrowException(new ValidationException(['password' => ['Слишком короткий']]))
        ;

        try {
            $this->validator->validate('short', 'different');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('password', $e->errors);
            $this->assertCount(2, $e->errors['password']);
        }
    }
}
