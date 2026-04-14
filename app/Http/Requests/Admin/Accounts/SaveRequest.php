<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Accounts;

use App\Http\Requests\AbstractRequest;
use App\Models\Account\Account;
use Core\Domains\Enums\Regexp;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const string ACCOUNT_REGEXP = Regexp::ACCOUNT_NAME;

    private const string ID           = 'id';
    private const string NUMBER       = 'number';
    private const string SIZE         = 'size';
    private const string IS_INVOICING = 'is_invoicing';

    public function rules(): array
    {
        return [
            self::NUMBER       => [
                'required',
                'string',
                'regex:' . self::ACCOUNT_REGEXP,
                Rule::unique(Account::TABLE, Account::NUMBER)->ignore($this->get(self::ID)),
            ],
            self::SIZE         => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NUMBER . '.required' => sprintf('Укажите «%s»', Account::TITLE_NUMBER),
            self::NUMBER . '.string'   => sprintf('Укажите «%s»', Account::TITLE_NUMBER),
            self::NUMBER . '.regex'    => sprintf('Неверный формат для «%s»', Account::TITLE_NUMBER),
            self::NUMBER . '.unique'   => sprintf('Значение для «%s» уже существует', Account::TITLE_NUMBER),

            self::SIZE . '.required' => sprintf('Укажите «%s»', Account::TITLE_SIZE),
            self::SIZE . '.numeric'  => sprintf('Укажите число для «%s»', Account::TITLE_SIZE),
            self::SIZE . '.min'      => sprintf('Минимальное значение для «%s» - :min', Account::TITLE_SIZE),
        ];
    }

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getNumber(): ?string
    {
        return $this->getStringOrNull(self::NUMBER);
    }

    public function getSize(): ?int
    {
        return $this->getIntOrNull(self::SIZE);
    }

    public function getCadastreNumber(): ?string
    {
        return $this->getStringOrNull('cadastreNumber');
    }

    public function getIsInvoicing(): bool
    {
        return $this->getBool(self::IS_INVOICING);
    }
}
