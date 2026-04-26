<?php declare(strict_types=1);

namespace Core\App\User;

use Core\Exceptions\ValidationException;

class SetPasswordByTokenValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(?string $password, ?string $passwordConfirmation): void
    {
        $errors = [];

        if ($password === null || $password === '') {
            $errors['password'][] = 'Заполните поле "пароль"';
        } else {
            if (mb_strlen($password) < 8) {
                $errors['password'][] = 'Количество символов должно быть не меньше 8';
            }

            if (! preg_match('/[a-z]/', $password) || ! preg_match('/[A-Z]/', $password)) {
                $errors['password'][] = 'Пароль должен содержать строчные и заглавные буквы';
            }

            if (! preg_match('/\d/', $password)) {
                $errors['password'][] = 'Пароль должен содержать цифры';
            }
        }

        if ($password !== $passwordConfirmation) {
            $errors['password'][] = 'Пароли не совпадают';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
