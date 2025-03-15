<?php declare(strict_types=1);

namespace App\Http\Requests\Profile\Accounts;

use App\Http\Requests\AbstractRequest;
use App\Models\Account\Account;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Enums\Regexp;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class RegisterRequest extends AbstractRequest
{
    private const ACCOUNT_REGEXP = Regexp::ACCOUNT_NAME;

    private const NUMBER = RequestArgumentsEnum::NUMBER;
    private const SIZE   = 'size';

    public function rules(): array
    {
        return [
            self::NUMBER => [
                'required',
                'string',
                'min:3',
                'regex:' . self::ACCOUNT_REGEXP,
                Rule::unique(Account::TABLE, Account::NUMBER),
            ],
            self::SIZE   => [
                'required',
                'numeric',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NUMBER . '.required' => 'Укажите номер участка',
            self::NUMBER . '.unique'   => 'Такой участок уже зарегистрирован',
            self::SIZE . '.required'   => 'Укажите площадь участка',
            self::SIZE . '.numeric'    => 'Площадь участка должна быть числом',
        ];
    }

    public function attributes(): array
    {
        return [
            self::NUMBER => 'Номер участка',
        ];
    }

    public function getNumber(): string
    {
        return $this->getString(self::NUMBER);
    }

    public function getSize(): int
    {
        return $this->getInt(self::SIZE);
    }


    public function dto(): AccountDTO
    {
        $dto = new AccountDTO();

        $dto->setNumber($this->get(self::NUMBER))
            ->setSize($this->getInt(self::SIZE ));

        return $dto;
    }
}
