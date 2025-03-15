<?php declare(strict_types=1);

namespace App\Http\Requests\Public\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\Enums\Regexp;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Http\UploadedFile;

class CounterCreateRequest extends AbstractRequest
{
    private const ACCOUNT_REGEXP = Regexp::ACCOUNT_NAME;

    private const EMAIL   = RequestArgumentsEnum::EMAIL;
    private const PHONE   = RequestArgumentsEnum::PHONE;
    private const NAME    = RequestArgumentsEnum::NAME;
    private const COUNTER = RequestArgumentsEnum::COUNTER;
    private const ACCOUNT = RequestArgumentsEnum::ACCOUNT;
    private const VALUE   = RequestArgumentsEnum::VALUE;
    private const FILE    = RequestArgumentsEnum::FILE;

    public function rules(): array
    {
        return [
            self::EMAIL   => [
                'nullable',
                'string',
            ],
            self::PHONE   => [
                'nullable',
                'string',
            ],
            self::NAME    => [
                'nullable',
                'string',
            ],
            self::COUNTER => [
                'required',
                'string',
            ],
            self::ACCOUNT => [
                'required',
                'string',
                'regex:' . self::ACCOUNT_REGEXP,
            ],
            self::VALUE   => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            self::ACCOUNT => 'Номер дачи/участка',
        ];
    }

    public function getFile(): UploadedFile
    {
        return $this->file(self::FILE);
    }

    public function getFullText(): string
    {
        return sprintf("%s%s%s%s%s%s%s",
            $this->getFormattedAccount(),
            $this->getFormattedCounter(),
            $this->getFormattedValue(),
            $this->getFormattedName(),
            $this->getFormattedEmail(),
            $this->getFormattedPhone(),
            $this->getFormattedAttachmentsList(),
        );
    }

    public function getName(): string
    {
        return (string) $this->getStringOrNull(self::NAME);
    }

    public function getEmail(): string
    {
        return (string) $this->getStringOrNull(self::EMAIL);
    }

    public function getPhone(): string
    {
        return (string) $this->getStringOrNull(self::PHONE);
    }

    public function getCounter(): string
    {
        return (string) $this->getStringOrNull(self::COUNTER);
    }

    public function getAccount(): string
    {
        return (string) $this->getStringOrNull(self::ACCOUNT);
    }

    public function getValue(): int
    {
        return $this->getInt(self::VALUE);
    }

    private function getFormattedAccount(): string
    {
        return $this->getAccount() ? sprintf("Участок: %s\n", $this->getAccount()) : '';
    }

    private function getFormattedCounter(): string
    {
        return $this->getCounter() ? sprintf("Счётчик №: %s\n", $this->getCounter()) : '';
    }

    private function getFormattedValue(): string
    {
        return $this->getValue() ? sprintf("Показание: %s\n", $this->getValue()) : '';
    }

    private function getFormattedEmail(): string
    {
        return $this->getEmail() ? sprintf("Почта для связи: %s\n", $this->getEmail()) : '';
    }

    private function getFormattedName(): string
    {
        return $this->getName() ? sprintf("Обращение от: %s\n", $this->getName()) : '';
    }

    private function getFormattedPhone(): string
    {
        return $this->getPhone() ? sprintf("Телефон для связи: %s\n", $this->getPhone()) : '';
    }

    private function getFormattedAttachmentsList(): string
    {
        $files  = $this->allFiles();
        $result = [];
        $i      = 1;
        foreach ($files as $file) {
            $result[] = sprintf('%d. %s', $i++, $file->getClientOriginalName());
        }

        return $result ? sprintf("Вложения: \n%s\n", implode("\n", $result)) : '';
    }
}
