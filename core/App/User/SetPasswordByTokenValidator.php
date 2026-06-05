<?php declare(strict_types=1);

namespace Core\App\User;

use Core\Exceptions\ValidationException;

class SetPasswordByTokenValidator
{
    public function __construct(
        private PasswordPolicyValidator $passwordPolicyValidator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function validate(?string $password, ?string $passwordConfirmation): void
    {
        $errors = [];

        try {
            $this->passwordPolicyValidator->validate($password);
        }
        catch (ValidationException $exception) {
            $errors = array_merge_recursive($errors, $exception->errors);
        }

        if ($password !== $passwordConfirmation) {
            $errors['password'][] = 'Пароли не совпадают';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
