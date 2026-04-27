<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Counters;

use App\Http\Requests\AbstractRequest;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const string ID            = 'id';
    private const string NUMBER        = 'number';
    private const string IS_INVOICING  = 'isInvoicing';
    private const string INCREMENT     = 'increment';
    private const string EXPIRE_AT     = 'expireAt';
    private const string PASSPORT_FILE = 'passportFile';

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

    public function getPassportFile(): ?UploadedFile
    {
        return $this->file(self::PASSPORT_FILE);
    }

    public function getExpireAt(): ?Carbon
    {
        return $this->getDateOrNull(self::EXPIRE_AT);
    }
}
