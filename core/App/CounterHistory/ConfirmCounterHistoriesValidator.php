<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Exceptions\ValidationException;

class ConfirmCounterHistoriesValidator
{
    public function validate(array $ids): void
    {
        if ($ids === []) {
            throw new ValidationException(['ids' => ['Не выбраны элементы']]);
        }
    }
}
