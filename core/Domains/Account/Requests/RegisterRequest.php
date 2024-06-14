<?php declare(strict_types=1);

namespace Core\Domains\Account\Requests;

use App\Http\Requests\AbstractRequest;
use App\Models\Account\Account;
use Core\Domains\Account\Models\AccountDTO;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class RegisterRequest extends AbstractRequest
{
    private const NUMBER = RequestArgumentsEnum::NUMBER;

    public function rules(): array
    {
        return [
            self::NUMBER => [
                'required',
                'string',
                'min:3',
                'regex:/^(\d+)\/(\d)$/',
                Rule::unique(Account::TABLE, Account::NUMBER),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NUMBER . '.required' => 'Укажите номер участка',
            self::NUMBER . '.unique'   => 'Такой участок уже зарегистрирован',
        ];
    }

    public function attributes()
    {
        return [
            self::NUMBER => 'Номер участка',
        ];
    }


    public function dto(): AccountDTO
    {
        $dto = new AccountDTO();

        $dto->setNumber($this->get(self::NUMBER));

        return $dto;
    }
}
