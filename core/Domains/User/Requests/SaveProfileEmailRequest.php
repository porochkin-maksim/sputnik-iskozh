<?php declare(strict_types=1);

namespace Core\Domains\User\Requests;

use App\Http\Requests\AbstractRequest;
use App\Models\User;
use Core\Domains\User\Models\UserDTO;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveProfileEmailRequest extends AbstractRequest
{
    private const EMAIL = RequestArgumentsEnum::EMAIL;

    public function rules(): array
    {
        return [
            self::EMAIL => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::TABLE, User::EMAIL),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::EMAIL . '.required' => 'Заполните поле "эл.почта"',
            self::EMAIL . '.unique'   => 'Адрес уже занят',
            self::EMAIL . '.string'   => 'Поле должно быть строкой',
            self::EMAIL . '.email'    => 'Поле должно быть корретным адресом эл.почты',
            self::EMAIL . '.max'      => 'Количество символов должно быть меньше :max',
        ];
    }

    public function dto(UserDTO $dto = null): UserDTO
    {
        $dto = $dto ?: new UserDTO();
        $dto->setEmail($this->get(self::EMAIL));

        return $dto;
    }
}
