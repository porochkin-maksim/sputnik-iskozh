<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Exceptions\ValidationException;

class SaveValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(string $name): void
    {
        $errors = [];
        $name = trim($name);

        if ($name === '') {
            $errors['name'][] = 'Укажите название файла';
        }
        elseif (mb_strlen($name) < 3) {
            $errors['name'][] = 'Название файла должно быть не короче 3 символов';
        }
        elseif (mb_strlen($name) > 255) {
            $errors['name'][] = 'Название файла не должно превышать 255 символов';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
