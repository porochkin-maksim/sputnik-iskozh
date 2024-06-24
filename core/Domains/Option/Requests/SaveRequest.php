<?php declare(strict_types=1);

namespace Core\Domains\Option\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\Option\Models\OptionDTO;
use Core\Requests\RequestArgumentsEnum;

class SaveRequest extends AbstractRequest
{
    private const ID = RequestArgumentsEnum::ID;
    private const DATA = RequestArgumentsEnum::DATA;

    public function rules(): array
    {
        return [
            self::ID => [
                'required',
                'numeric',
            ],
            self::DATA => [
                'required',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::DATA . '.required' => 'Укажите значение',
        ];
    }

    public function dto(): OptionDTO
    {
        $dto = new OptionDTO();

        $dto->setId($this->getInt(self::ID))
            ->setData($this->get(self::DATA));

        return $dto;
    }
}
