<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Counters;

use App\Http\Requests\AbstractRequest;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const ID           = RequestArgumentsEnum::ID;
    private const NUMBER       = RequestArgumentsEnum::NUMBER;
    private const IS_INVOICING = 'is_invoicing';
    private const INCREMENT    = RequestArgumentsEnum::INCREMENT;

    public function rules(): array
    {
        return [
            self::NUMBER => [
                'required',
                Rule::unique('counters', 'number')->ignore($this->get(self::ID)),
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

    public function getId(): int
    {
        return $this->getInt(self::ID);
    }

    public function getNumber(): string
    {
        return $this->getString(self::NUMBER);
    }

    public function getIsInvoicing(): bool
    {
        return $this->getBool(self::IS_INVOICING);
    }

    public function getIncrement(): int
    {
        return abs($this->getInt(self::INCREMENT));
    }
}
