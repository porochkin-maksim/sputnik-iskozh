<?php declare(strict_types=1);

namespace App\Http\Requests\Profile\Counters;

use App\Http\Requests\AbstractRequest;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class CreateRequest extends AbstractRequest
{
    private const string NUMBER        = 'number';
    private const string VALUE         = 'value';
    private const string INCREMENT     = 'increment';
    private const string FILE          = 'file';
    private const string PASSPORT_FILE = 'passportFile';
    private const string EXPIRE_AT     = 'expireAt';

    public function rules(): array
    {
        return [
            self::NUMBER => [
                'required',
                Rule::unique('counters', 'number'),
            ],
            self::VALUE  => [
                'required',
            ],
            self::FILE   => [
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

    public function getHistoryFile(): UploadedFile
    {
        return $this->file(self::FILE);
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
