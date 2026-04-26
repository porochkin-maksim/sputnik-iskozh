<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Exceptions\ValidationException;

class CreatePublicPaymentValidator
{
    public function validate(string $text, float $cost): void
    {
        $errors = [];

        if (trim($text) === '') {
            $errors['text'][] = 'Поле «Текст обращения» обязательно';
        }

        if ($cost < 0) {
            $errors['cost'][] = 'Стоимость должна быть больше или равна 0';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
