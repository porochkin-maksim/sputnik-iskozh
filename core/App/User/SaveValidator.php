<?php declare(strict_types=1);

namespace Core\App\User;

use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;

readonly class SaveValidator
{
    public function __construct(
        private UserService $userService,
    )
    {
    }

    public function validate(?int $id, ?string $email): void
    {
        $errors = [];

        if ($email === null || trim($email) === '') {
            $errors['email'][] = 'Заполните поле "эл.почта"';
        } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Поле должно быть корретным адресом эл.почты';
        } elseif (mb_strlen($email) > 255) {
            $errors['email'][] = 'Количество символов должно быть меньше 255';
        } else {
            $user = $this->userService->getByEmail($email);
            if ($user !== null && $user->getId() !== $id) {
                $errors['email'][] = 'Адрес уже занят';
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
