<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

use App\Models\Billing\Claim;
use App\Models\Billing\Invoice;
use App\Models\Billing\Service;
use Core\Exceptions\ValidationException;

class SaveValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(
        ?int $invoiceId,
        ?int $serviceId,
        ?float $tariff,
        ?float $cost,
        ?string $name,
    ): void
    {
        $errors = [];

        if ($invoiceId === null) {
            $errors['invoice_id'][] = sprintf('Укажите «%s»', Claim::TITLE_INVOICE_ID);
        }
        elseif (Invoice::query()->whereKey($invoiceId)->exists() === false) {
            $errors['invoice_id'][] = sprintf('Указанный «%s» не существует', Claim::TITLE_INVOICE_ID);
        }

        if ($serviceId === null) {
            $errors['service_id'][] = sprintf('Укажите «%s»', Claim::TITLE_SERVICE_ID);
        }
        elseif (Service::query()->whereKey($serviceId)->exists() === false) {
            $errors['service_id'][] = sprintf('Указанный «%s» не существует', Claim::TITLE_SERVICE_ID);
        }

        if ($tariff === null) {
            $errors['tariff'][] = sprintf('Укажите «%s»', Claim::TITLE_TARIFF);
        }
        elseif ($tariff < 0) {
            $errors['tariff'][] = sprintf('«%s» должна быть больше 0', Claim::TITLE_TARIFF);
        }

        if ($cost === null) {
            $errors['cost'][] = sprintf('Укажите «%s»', Claim::TITLE_COST);
        }
        elseif ($cost < 0) {
            $errors['cost'][] = sprintf('«%s» должна быть больше 0', Claim::TITLE_COST);
        }

        if ($name !== null && mb_strlen($name) > 255) {
            $errors['name'][] = sprintf('«%s» не должно превышать 255 символов', Claim::TITLE_NAME);
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
