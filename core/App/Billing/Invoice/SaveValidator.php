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

        $period = $periodId ? $this->periodService->getById($periodId) : null;
        if ($period === null) {
            $errors['period_id'][] = 'Указанный «Период» не существует';
        }
        elseif ($period->isClosed()) {
            $errors['period_id'][] = 'Период закрыт, редактирование невозможно';
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
