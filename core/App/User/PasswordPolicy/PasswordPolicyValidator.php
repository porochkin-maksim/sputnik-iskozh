<?php declare(strict_types=1);

namespace Core\App\User\PasswordPolicy;

use Core\Exceptions\ValidationException;

class PasswordPolicyValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(?string $password): void
    {
        $errors = [];

        if ($password === null || $password === '') {
            $errors['password'][] = 'Заполните поле "пароль"';
        } else {
            if (mb_strlen($password) < PasswordPolicy::MIN_LENGTH) {
                $errors['password'][] = 'Количество символов должно быть не меньше ' . PasswordPolicy::MIN_LENGTH;
            }

            if (! preg_match(PasswordPolicy::LOWERCASE_PATTERN, $password) || ! preg_match(PasswordPolicy::UPPERCASE_PATTERN, $password)) {
                $errors['password'][] = 'Пароль должен содержать строчные и заглавные буквы';
            }

            if (! preg_match(PasswordPolicy::DIGIT_PATTERN, $password)) {
                $errors['password'][] = 'Пароль должен содержать цифры';
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
