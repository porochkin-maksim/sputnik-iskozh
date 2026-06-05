<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodService;
use Core\Exceptions\ValidationException;

readonly class SaveValidator
{
    public function __construct(
        private PeriodService $periodService,
        private AccountService $accountService,
    )
    {
    }

    public function validate(?int $periodId, ?int $accountId, ?int $type, ?string $name): void
    {
        $errors = [];

        if ($periodId === null || $this->periodService->getById($periodId) === null) {
            $errors['period_id'][] = 'Указанный «Период» не существует';
        }

        if ($accountId === null || $this->accountService->getById($accountId) === null) {
            $errors['account_id'][] = 'Указанный «Участок» не существует';
        }

        if ($type === null || InvoiceTypeEnum::tryFrom($type) === null) {
            $errors['type'][] = 'Неверный «Тип»';
        }

        if ($name !== null && mb_strlen($name) > 191) {
            $errors['name'][] = 'Слишком длинное «Название»';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
