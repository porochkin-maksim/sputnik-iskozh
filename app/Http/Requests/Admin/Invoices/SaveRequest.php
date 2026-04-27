<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Invoices;

use App\Http\Requests\AbstractRequest;
use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Models\Billing\Period;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const string ID         = 'id';
    private const string PERIOD_ID  = 'period_id';
    private const string ACCOUNT_ID = 'account_id';
    private const string TYPE       = 'type';
    private const string NAME       = 'name';

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
            self::PERIOD_ID . '.required' => sprintf('Укажите «%s»', Invoice::TITLE_PERIOD_ID),
            self::PERIOD_ID . '.exists'   => sprintf('Указанный «%s» не существует', Invoice::TITLE_PERIOD_ID),

            self::ACCOUNT_ID . '.required' => sprintf('Укажите «%s»', Invoice::TITLE_ACCOUNT_ID),
            self::ACCOUNT_ID . '.exists'   => sprintf('Указанный «%s» не существует', Invoice::TITLE_ACCOUNT_ID),

            self::TYPE . '.required' => sprintf('Укажите «%s»', Invoice::TITLE_TYPE),
            self::TYPE . '.in'       => sprintf('Неверный «%s»', Invoice::TITLE_TYPE),

            self::NAME . '.max' => sprintf('Слишком длинное «%s»', Invoice::TITLE_NAME),
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
        return $this->getStringOrNull(self::NAME);
    }
}
