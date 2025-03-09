<?php declare(strict_types=1);

namespace App\Http\Requests\Users;

use App\Http\Requests\AbstractRequest;
use Core\Domains\User\Models\UserDTO;
use Core\Requests\RequestArgumentsEnum;

class SaveProfileRequest extends AbstractRequest
{
    private const LAST_NAME   = RequestArgumentsEnum::LAST_NAME;
    private const FIRST_NAME  = RequestArgumentsEnum::FIRST_NAME;
    private const MIDDLE_NAME = RequestArgumentsEnum::MIDDLE_NAME;

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
