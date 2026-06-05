<?php declare(strict_types=1);

namespace Tests\Unit\App\User;

use Core\App\User\PasswordPolicyValidator;
use Core\App\User\SaveProfilePasswordValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveProfilePasswordValidatorTest extends TestCase
{
    private SaveProfilePasswordValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $passwordPolicyValidator = $this->createMock(PasswordPolicyValidator::class);
        $this->validator         = new SaveProfilePasswordValidator($passwordPolicyValidator);
    }

    public function test_valid_password_passes(): void
    {
        $passwordPolicyValidator = $this->createMock(PasswordPolicyValidator::class);
        $passwordPolicyValidator->expects($this->once())
            ->method('validate')
            ->with('Valid1Pass')
        ;

        $validator = new SaveProfilePasswordValidator($passwordPolicyValidator);
        $validator->validate('Valid1Pass');
    }

    public function test_empty_password_forwards_to_policy(): void
    {
        $passwordPolicyValidator = $this->createMock(PasswordPolicyValidator::class);
        $passwordPolicyValidator->expects($this->once())
            ->method('validate')
            ->with('')
            ->willThrowException(new ValidationException(['password' => ['Заполните поле "пароль"']]))
        ;

        $validator = new SaveProfilePasswordValidator($passwordPolicyValidator);

        $this->expectException(ValidationException::class);
        $validator->validate('');
    }
}
