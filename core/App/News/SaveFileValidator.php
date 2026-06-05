<?php declare(strict_types=1);

namespace Core\App\News;

use Core\Exceptions\ValidationException;

class SaveFileValidator
{
    public function validate(?string $name): void
    {
        $errors = [];

        if ($name === null || trim($name) === '') {
            $errors['name'][] = 'Имя файла обязательно';
        } elseif (mb_strlen($name) < 3) {
            $errors['name'][] = 'Имя файла должно содержать минимум 3 символа';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
