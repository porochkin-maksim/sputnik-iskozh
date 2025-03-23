<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Transactions;

use App\Http\Requests\AbstractRequest;
use App\Models\Billing\Invoice;
use App\Models\Billing\Service;
use Core\Domains\Billing\Transaction\Models\TransactionComparator;
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
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::INVOICE_ID . '.required' => sprintf('Укажите «%s»', TransactionComparator::TITLE_INVOICE_ID),
            self::INVOICE_ID . '.exists'   => sprintf('Указанный «%s» не существует', TransactionComparator::TITLE_INVOICE_ID),

            self::SERVICE_ID . '.required' => sprintf('Укажите «%s»', TransactionComparator::TITLE_SERVICE_ID),
            self::SERVICE_ID . '.exists'   => sprintf('Указанный «%s» не существует', TransactionComparator::TITLE_SERVICE_ID),

            self::TARIFF . '.required' => sprintf('Укажите «%s»', TransactionComparator::TITLE_TARIFF),
            self::TARIFF . '.numeric'  => sprintf('«%s» должна быть числом', TransactionComparator::TITLE_TARIFF),
            self::TARIFF . '.min'      => sprintf('«%s» должна быть больше :min', TransactionComparator::TITLE_TARIFF),

            self::COST . '.required' => sprintf('Укажите «%s»', TransactionComparator::TITLE_COST),
            self::COST . '.numeric'  => sprintf('«%s» должна быть числом', TransactionComparator::TITLE_COST),
            self::COST . '.min'      => sprintf('«%s» должна быть больше :min', TransactionComparator::TITLE_COST),
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
