<?php declare(strict_types=1);

namespace Core\Auth\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Objects\User\Models\UserDTO;
use Core\Requests\RequestArgumentsEnum;
use Core\Requests\Rules;

class RegisterRequest extends AbstractRequest
{
    private const LOGIN    = RequestArgumentsEnum::EMAIL;
    private const PASSWORD = RequestArgumentsEnum::PASSWORD;

    public function rules(): array
    {
        return [
            self::LOGIN    => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            self::PASSWORD => Rules::Password(),
        ];
    }

    public function messages(): array
    {
        return [
            self::LOGIN . '.required' => 'Заполните поле "эл.почта"',
            self::LOGIN . '.unique'   => 'Адрес уже занят',
            self::LOGIN . '.string'   => 'Поле должно быть строкой',
            self::LOGIN . '.email'    => 'Поле должно быть корретным адресом эл.почты',
            self::LOGIN . '.max'      => 'Количество символов должно быть меньше :max',

            self::PASSWORD . '.required'  => 'Заполните поле "пароль"',
            self::PASSWORD . '.string'    => 'Поле должно быть строкой',
            self::PASSWORD . '.confirmed' => 'Пароли не совпадают',
            self::PASSWORD . '.min'       => 'Количество символов должно быть не меньше :min',
            self::PASSWORD . '.mixed'     => 'Пароль должен содержать строчные и заглавные буквы',
            self::PASSWORD . '.numbers'   => 'Пароль должен содержать цифры',
            self::PASSWORD . '.symbols'   => 'Пароль должен содержать спец.символы',
        ];
    }

    public function dto(): UserDTO
    {
        $dto = new UserDTO();
        $dto->setEmail($this->get(self::LOGIN))
            ->setPassword($this->get(self::PASSWORD));

        return $dto;
    }
}
