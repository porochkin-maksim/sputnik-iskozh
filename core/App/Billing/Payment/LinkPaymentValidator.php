<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Domains\Account\AccountService;
use Core\Exceptions\ValidationException;

readonly class LinkPaymentValidator
{
    public function __construct(
        private AccountService $accountService,
    )
    {
    }

    public function validate(?float $cost, ?int $accountId): void
    {
        $errors = [];

        if ($cost === null) {
            $errors['cost'][] = 'Укажите «Сумма»';
        } elseif ($cost < 0) {
            $errors['cost'][] = '«Сумма» должна быть больше 0';
        }

        if ($accountId === null || $this->accountService->getById($accountId) === null) {
            $errors['account_id'][] = 'Указанный «Участок» не существует';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
