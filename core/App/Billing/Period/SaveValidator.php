<?php declare(strict_types=1);

namespace Core\App\Billing\Period;

use Carbon\Carbon;
use Core\Exceptions\ValidationException;

class SaveValidator
{
    public function validate(?string $name, ?Carbon $startAt, ?Carbon $endAt): void
    {
        $errors = [];

        if ($name === null || trim($name) === '') {
            $errors['name'][] = 'Укажите «Название»';
        }

        if ($startAt === null) {
            $errors['start_at'][] = 'Укажите «Дата начала»';
        }

        if ($endAt === null) {
            $errors['end_at'][] = 'Укажите «Дата окончания»';
        }

        if ($startAt !== null && $endAt !== null && $startAt->gt($endAt)) {
            $errors['end_at'][] = '«Дата окончания» должна быть не раньше «Даты начала»';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
