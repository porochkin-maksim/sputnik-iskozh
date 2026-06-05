<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Exceptions\ValidationException;

class LinkCounterHistoryValidator
{
    public function validate(?int $id, ?int $counterId): void
    {
        $errors = [];

        if ($id === null) {
            $errors['id'][] = 'Не указана запись показаний';
        }

        if ($counterId === null) {
            $errors['counter_id'][] = 'Не указан счётчик';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
