<?php declare(strict_types=1);

namespace Core\App\Account;

use Core\Domains\Account\AccountService;
use Core\Domains\Enums\Regexp;
use Core\Exceptions\ValidationException;

readonly class SaveValidator
{
    public function __construct(
        private AccountService $accountService,
    )
    {
    }

    public function validate(?int $id, ?string $number, ?int $size): void
    {
        $errors = [];

        if ($number === null || trim($number) === '') {
            $errors['number'][] = 'Укажите «Номер участка»';
        } elseif (preg_match('/' . Regexp::ACCOUNT_NAME . '/', $number) !== 1) {
            $errors['number'][] = 'Неверный формат для «Номер участка»';
        } else {
            $exists = $this->accountService->findByNumber($number);
            if ($exists !== null && $exists->getId() !== $id) {
                $errors['number'][] = 'Значение для «Номер участка» уже существует';
            }
        }

        if ($size === null) {
            $errors['size'][] = 'Укажите «Площадь»';
        } elseif ($size < 0) {
            $errors['size'][] = 'Минимальное значение для «Площадь» - 0';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
