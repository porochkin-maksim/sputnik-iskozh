<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Payments;

use App\Http\Requests\AbstractRequest;
use App\Models\Account\Account;
use Core\Domains\Billing\Payment\Models\PaymentComparator;
use Illuminate\Validation\Rule;

class LinkRequest extends AbstractRequest
{
    private const string ID         = 'id';
    private const string NAME       = 'name';
    private const string COST       = 'cost';
    private const string COMMENT    = 'comment';
    private const string ACCOUNT_ID = 'account_id';
    private const string INVOICE_ID = 'invoice_id';

    public function rules(): array
    {
        return [
            self::COST       => [
                'required',
                'numeric',
                'min:0',
            ],
            self::NAME    => [
                'nullable',
                'string',
            ],
            self::COMMENT    => [
                'nullable',
                'string',
            ],
            self::ACCOUNT_ID => [
                'required',
                Rule::exists(Account::TABLE, Account::ID),
            ],
            self::INVOICE_ID => [
                'sometimes',
                // Rule::exists(Invoice::TABLE, Invoice::ID),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::COST . '.required' => sprintf('Укажите «%s»', PaymentComparator::TITLE_COST),
            self::COST . '.numeric'  => sprintf('«%s» должна быть числом', PaymentComparator::TITLE_COST),
            self::COST . '.min'      => sprintf('«%s» должна быть больше :min', PaymentComparator::TITLE_COST),

            self::ACCOUNT_ID . '.required' => sprintf('Укажите «%s»', PaymentComparator::TITLE_ACCOUNT_ID),
            self::ACCOUNT_ID . '.exists'   => sprintf('Указанный «%s» не существует', PaymentComparator::TITLE_ACCOUNT_ID),

            self::INVOICE_ID . '.required' => sprintf('Укажите «%s»', PaymentComparator::TITLE_INVOICE_ID),
            self::INVOICE_ID . '.exists'   => sprintf('Указанный «%s» не существует', PaymentComparator::TITLE_INVOICE_ID),
        ];
    }

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getCost(): float
    {
        return $this->getFloat(self::COST);
    }

    public function getAccountId(): int
    {
        return $this->getIntOrNull(self::ACCOUNT_ID);
    }

    public function getInvoiceId(): ?int
    {
        return $this->getInt(self::INVOICE_ID) ?: null;
    }

    public function getName(): ?string
    {
        return $this->getStringOrNull(self::NAME);
    }

    public function getComment(): ?string
    {
        return $this->getStringOrNull(self::COMMENT);
    }
}
