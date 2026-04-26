<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Exceptions\ValidationException;

class AddAdminCounterHistoryValidator
{
    public function validate(?int $counterId, mixed $value): void
    {
        $errors = [];

        if ($counterId === null) {
            $errors['counter_id'][] = 'Не указан счётчик';
        }

        if ($value === null || $value === '' || (int) $value < 0) {
            $errors['value'][] = 'Показание должно быть не меньше 0';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
