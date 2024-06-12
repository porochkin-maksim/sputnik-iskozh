<?php declare(strict_types=1);

namespace Core\Objects\Account\Requests;

use App\Http\Requests\AbstractRequest;
use App\Models\Account\Account;
use Core\Objects\Account\Models\AccountDTO;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const ID     = RequestArgumentsEnum::ID;
    private const NUMBER = RequestArgumentsEnum::NUMBER;

    public function rules(): array
    {
        return [
            self::NUMBER => [
                'required',
                'string',
                'min:3',
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

    public function dto(): AccountDTO
    {
        $dto = new AccountDTO();

        $dto->setId($this->getInt(self::ID))
            ->setNumber($this->get(self::NUMBER));

        return $dto;
    }
}
