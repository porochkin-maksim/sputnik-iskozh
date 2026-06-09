<?php declare(strict_types=1);

namespace Core\App\Billing\Service;

use Carbon\Carbon;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Exceptions\ValidationException;

readonly class SaveValidator
{
    public function __construct(
        private PeriodService $periodService,
    )
    {
    }

    public function validate(?int $periodId, ?ServiceTypeEnum $type, ?string $name, ?float $cost, ?Carbon $periodFrom = null, ?Carbon $periodTo = null): void
    {
        $errors = [];

        if ($periodId === null || $this->periodService->getById($periodId) === null) {
            $errors['period_id'][] = 'Указанный период не существует';
        }

        if ($type === null) {
            $errors['type'][] = 'Укажите тип услуги';
        }

        if ($name === null || trim($name) === '') {
            $errors['name'][] = 'Укажите название услуги';
        }

        if ($cost === null) {
            $errors['cost'][] = 'Укажите стоимость';
        } elseif ($cost < 0) {
            $errors['cost'][] = 'Стоимость не может быть отрицательной';
        }

        if ($periodFrom !== null && $periodTo !== null && $periodFrom->greaterThan($periodTo)) {
            $errors['period_from'][] = 'Дата начала периода не может быть позже даты окончания';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
