<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Claims;

use App\Http\Requests\AbstractRequest;
use App\Models\Billing\Invoice;
use App\Models\Billing\Service;
use Core\Domains\Billing\Claim\Models\ClaimComparator;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const ID         = RequestArgumentsEnum::ID;
    private const INVOICE_ID = RequestArgumentsEnum::INVOICE_ID;
    private const SERVICE_ID = RequestArgumentsEnum::SERVICE_ID;
    private const TARIFF     = RequestArgumentsEnum::TARIFF;
    private const COST       = RequestArgumentsEnum::COST;
    private const NAME       = RequestArgumentsEnum::NAME;

    public function rules(): array
    {
        return [
            self::INVOICE_ID => [
                'required',
                Rule::exists(Invoice::TABLE, Invoice::ID),
            ],
            self::SERVICE_ID => [
                'required',
                Rule::exists(Service::TABLE, Service::ID),
            ],
            self::TARIFF     => [
                'required',
                'numeric',
                'min:0',
            ],
            self::COST       => [
                'required',
                'numeric',
                'min:0',
            ],
            self::NAME       => [
                'sometimes',
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::INVOICE_ID . '.required' => sprintf('Укажите «%s»', ClaimComparator::TITLE_INVOICE_ID),
            self::INVOICE_ID . '.exists'   => sprintf('Указанный «%s» не существует', ClaimComparator::TITLE_INVOICE_ID),

            self::SERVICE_ID . '.required' => sprintf('Укажите «%s»', ClaimComparator::TITLE_SERVICE_ID),
            self::SERVICE_ID . '.exists'   => sprintf('Указанный «%s» не существует', ClaimComparator::TITLE_SERVICE_ID),

            self::TARIFF . '.required' => sprintf('Укажите «%s»', ClaimComparator::TITLE_TARIFF),
            self::TARIFF . '.numeric'  => sprintf('«%s» должна быть числом', ClaimComparator::TITLE_TARIFF),
            self::TARIFF . '.min'      => sprintf('«%s» должна быть больше :min', ClaimComparator::TITLE_TARIFF),

            self::COST . '.required' => sprintf('Укажите «%s»', ClaimComparator::TITLE_COST),
            self::COST . '.numeric'  => sprintf('«%s» должна быть числом', ClaimComparator::TITLE_COST),
            self::COST . '.min'      => sprintf('«%s» должна быть больше :min', ClaimComparator::TITLE_COST),
        ];
    }

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getInvoiceId(): ?int
    {
        return $this->getIntOrNull(self::INVOICE_ID);
    }

    public function getServiceId(): ?int
    {
        return $this->getIntOrNull(self::SERVICE_ID);
    }

    public function getTariff(): float
    {
        return $this->getFloat(self::TARIFF);
    }

    public function getCost(): float
    {
        return $this->getFloat(self::COST);
    }

    public function getName(): ?string
    {
        return $this->getStringOrNull(self::NAME);
    }
}
