<?php declare(strict_types=1);

namespace App\Http\Requests\Profile\Accounts;

use App\Http\Requests\AbstractRequest;
use App\Models\Account\Account;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Enums\Regexp;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const ACCOUNT_REGEXP = Regexp::ACCOUNT_NAME;

    private const ID     = RequestArgumentsEnum::ID;
    private const NUMBER = RequestArgumentsEnum::NUMBER;

    public function rules(): array
    {
        return [
            self::NUMBER => [
                'required',
                'string',
                'min:3',
                'regex:' . self::ACCOUNT_REGEXP,
                Rule::unique(Account::TABLE, Account::NUMBER)->ignore($this->get(self::ID)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NUMBER . '.required' => 'Укажите номер участка',
            self::NUMBER . '.string'   => 'Поле должно быть строкой',
            self::NUMBER . '.min'      => 'Поле должно быть не менее :min символов',
            self::NUMBER . '.unique'   => 'Такой участок уже зарегистрирован',
        ];
    }

    public function dto(): AccountDTO
    {
        $dto = new AccountDTO();

        $dto->setId($this->getInt(self::ID))
            ->setNumber($this->get(self::NUMBER));

        return $dto;
    }
}
