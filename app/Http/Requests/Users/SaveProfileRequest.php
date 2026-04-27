<?php declare(strict_types=1);

namespace App\Http\Requests\Users;

use App\Http\Requests\AbstractRequest;
use Core\Domains\User\Models\UserDTO;

class SaveProfileRequest extends AbstractRequest
{
    private const string LAST_NAME   = 'last_name';
    private const string FIRST_NAME  = 'first_name';
    private const string MIDDLE_NAME = 'middle_name';

    public function rules(): array
    {
        return [

        ];
    }

    public function messages(): array
    {
        return [

        ];
    }

    public function dto(UserDTO $dto = null): UserDTO
    {
        $dto = $dto ?: new UserDTO();
        $dto->setLastName($this->get(self::LAST_NAME))
            ->setFirstName($this->get(self::FIRST_NAME))
            ->setMiddleName($this->get(self::MIDDLE_NAME));

        return $dto;
    }
}
