<?php declare(strict_types=1);

namespace Core\Domains\Counter\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\Counter\Enums\TypeEnum;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const NUMBER = RequestArgumentsEnum::NUMBER;

    public function rules(): array
    {
        return [
            self::NUMBER => [
                'required',
                Rule::unique('counters', 'number'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NUMBER . '.required' => 'Укажите номер счётчика',
            self::NUMBER . '.unique'   => 'Такой счётчик уже существует в системе',
        ];
    }

    public function dto(): CounterDTO
    {
        $dto = new CounterDTO();

        $dto->setType(TypeEnum::ELECTRICITY)
            ->setNumber($this->get(self::NUMBER));

        return $dto;
    }
}
