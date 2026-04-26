<?php declare(strict_types=1);

namespace Core\App\User;

use Core\Exceptions\ValidationException;

class ListValidator
{
    public function validate(?int $limit, ?int $offset, ?string $sortField, ?string $sortOrder): void
    {
        $errors = [];

        if ($limit !== null && ($limit < 1 || $limit > 1000)) {
            $errors['limit'][] = 'Лимит должен быть от 1 до 1000';
        }

        if ($offset !== null && $offset < 0) {
            $errors['skip'][] = 'Смещение не может быть отрицательным';
        }

        if ($sortField !== null && ! in_array($sortField, ['id', 'last_name', 'first_name', 'email', 'phone'], true)) {
            $errors['sort_field'][] = 'Недопустимое поле сортировки';
        }

        if ($sortOrder !== null && ! in_array($sortOrder, ['asc', 'desc'], true)) {
            $errors['sort_order'][] = 'Недопустимый порядок сортировки';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
