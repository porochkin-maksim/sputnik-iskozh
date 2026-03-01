<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Counters;

use App\Http\Requests\AbstractRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class CreateRequest extends AbstractRequest
{
    private const string NUMBER        = 'number';
    private const string VALUE         = 'value';
    private const string IS_INVOICING  = 'isInvoicing';
    private const string HISTORY_FILE  = 'file';
    private const string PASSPORT_FILE = 'passportFile';

    public function rules(): array
    {
        return [
            self::NUMBER       => [
                'required',
                Rule::unique('counters', 'number'),
            ],
            self::VALUE        => [
                'required',
            ],
            self::HISTORY_FILE => [
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

    public function getIsInvoicing(): bool
    {
        return $this->getBool(self::IS_INVOICING);
    }

    public function getHistoryFile(): ?UploadedFile
    {
        return $this->file(self::HISTORY_FILE);
    }

    public function getPassportFile(): ?UploadedFile
    {
        return $this->file(self::PASSPORT_FILE);
    }
}
