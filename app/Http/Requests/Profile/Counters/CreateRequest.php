<?php declare(strict_types=1);

namespace App\Http\Requests\Profile\Counters;

use App\Http\Requests\AbstractRequest;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class CreateRequest extends AbstractRequest
{
    private const NUMBER = RequestArgumentsEnum::NUMBER;
    private const VALUE  = 'value';
    private const FILE  = 'file';

    public function rules(): array
    {
        return [
            self::NUMBER => [
                'required',
                Rule::unique('counters', 'number'),
            ],
            self::VALUE => [
                'required',
            ],
            self::FILE => [
                'required',
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

    public function getNumber(): string
    {
        return $this->getString(self::NUMBER);
    }

    public function getValue(): int
    {
        return $this->getInt(self::VALUE);
    }

    public function getFile(): UploadedFile
    {
        return $this->file(self::FILE);
    }
}
