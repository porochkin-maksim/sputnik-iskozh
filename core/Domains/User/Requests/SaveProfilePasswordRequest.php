<?php declare(strict_types=1);

namespace Core\Domains\User\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\User\Models\UserDTO;
use Core\Requests\RequestArgumentsEnum;
use Core\Requests\Rules;

class SaveProfilePasswordRequest extends AbstractRequest
{
    private const PASSWORD = RequestArgumentsEnum::PASSWORD;

    public function rules(): array
    {
        return [
            self::PASSWORD => Rules::Password(),
        ];
    }

    public function messages(): array
    {
        return [
            self::PASSWORD . '.required'  => 'Заполните поле "пароль"',
            self::PASSWORD . '.string'    => 'Поле должно быть строкой',
            self::PASSWORD . '.confirmed' => 'Пароли не совпадают',
            self::PASSWORD . '.min'       => 'Количество символов должно быть не меньше :min',
            self::PASSWORD . '.mixed'     => 'Пароль должен содержать строчные и заглавные буквы',
            self::PASSWORD . '.numbers'   => 'Пароль должен содержать цифры',
            self::PASSWORD . '.symbols'   => 'Пароль должен содержать спец.символы',
        ];
    }

    public function dto(UserDTO $dto = null): UserDTO
    {
        $dto = $dto ?: new UserDTO();
        $dto->setPassword($this->get(self::PASSWORD));

        return $dto;
    }
}
