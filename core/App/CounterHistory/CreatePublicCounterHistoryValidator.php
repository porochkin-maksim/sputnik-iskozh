<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Domains\Enums\Regexp;
use Core\Exceptions\ValidationException;

class CreatePublicCounterHistoryValidator
{
    public function validate(string $accountNumber, ?string $counterNumber, int $value): void
    {
        $errors = [];

        if ($counterNumber === null || trim($counterNumber) === '') {
            $errors['counter'][] = 'Поле «Счётчик» обязательно';
        }

        if ($accountNumber === '' || preg_match(Regexp::ACCOUNT_NAME, $accountNumber) !== 1) {
            $errors['account'][] = 'Неверный формат номера дачи/участка';
        }

        if ($value < 0) {
            $errors['value'][] = 'Показание должно быть не меньше 0';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
