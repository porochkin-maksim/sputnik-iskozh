<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Invoices;

use App\Http\Requests\AbstractRequest;
use App\Models\Account\Account;
use App\Models\Billing\Period;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\Models\InvoiceComparator;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const string ID         = RequestArgumentsEnum::ID;
    private const string PERIOD_ID  = RequestArgumentsEnum::PERIOD_ID;
    private const string ACCOUNT_ID = RequestArgumentsEnum::ACCOUNT_ID;
    private const string TYPE       = RequestArgumentsEnum::TYPE;
    private const string NAME       = RequestArgumentsEnum::NAME;

    public function rules(): array
    {
        return [
            self::PERIOD_ID  => [
                'required',
                Rule::exists(Period::TABLE, Period::ID),
            ],
            self::ACCOUNT_ID => [
                'required',
                Rule::exists(Account::TABLE, Account::ID),
            ],
            self::TYPE       => [
                'required',
                'in:' . implode(',', InvoiceTypeEnum::values()),
            ],
            self::NAME       => [
                'string',
                'max:191',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::PERIOD_ID . '.required' => sprintf('Укажите «%s»', InvoiceComparator::TITLE_PERIOD_ID),
            self::PERIOD_ID . '.exists'   => sprintf('Указанный «%s» не существует', InvoiceComparator::TITLE_PERIOD_ID),

            self::ACCOUNT_ID . '.required' => sprintf('Укажите «%s»', InvoiceComparator::TITLE_ACCOUNT_ID),
            self::ACCOUNT_ID . '.exists'   => sprintf('Указанный «%s» не существует', InvoiceComparator::TITLE_ACCOUNT_ID),

            self::TYPE . '.required' => sprintf('Укажите «%s»', InvoiceComparator::TITLE_TYPE),
            self::TYPE . '.in'       => sprintf('Неверный «%s»', InvoiceComparator::TITLE_TYPE),

            self::NAME . '.max' => sprintf('Слишком длинное «%s»', InvoiceComparator::TITLE_NAME),
        ];
    }

    public function getId(): int
    {
        return $this->getInt(self::ID);
    }

    public function getPeriodId(): int
    {
        return $this->getInt(self::PERIOD_ID);
    }

    public function getAccountId(): int
    {
        return $this->getInt(self::ACCOUNT_ID);
    }

    public function getType(): ?int
    {
        return $this->getIntOrNull(self::TYPE);
    }

    public function getName(): ?string
    {
        return $this->getStringOrNull('name');
    }
}
