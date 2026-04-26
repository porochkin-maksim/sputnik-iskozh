<?php declare(strict_types=1);

namespace Core\App\Folders;

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
            $errors['name'][] = 'Укажите название папки';
        }
        elseif (mb_strlen($name) > 255) {
            $errors['name'][] = 'Название папки не должно превышать 255 символов';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
