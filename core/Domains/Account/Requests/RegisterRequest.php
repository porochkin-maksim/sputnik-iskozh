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
    private const SIZE   = 'size';

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

    public function attributes()
    {
        return [
            self::NUMBER => 'Номер участка',
        ];
    }


    public function dto(): AccountDTO
    {
        $dto = new AccountDTO();

        $dto->setNumber($this->get(self::NUMBER))
            ->setSize($this->getInt(self::SIZE ));

        return $dto;
    }
}
