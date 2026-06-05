<?php declare(strict_types=1);

namespace Core\App\Access;

use Core\Exceptions\ValidationException;

class SaveRoleValidator
{
    public function validate(?string $name, array $permissions): void
    {
        $errors = [];

        if ($name === null || trim($name) === '') {
            $errors['name'][] = 'Укажите название роли';
        }

        if ($permissions === []) {
            $errors['permissions'][] = 'Укажите права доступа';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
