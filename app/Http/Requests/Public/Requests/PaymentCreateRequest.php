<?php declare(strict_types=1);

namespace App\Http\Requests\Public\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Requests\RequestArgumentsEnum;

class PaymentCreateRequest extends AbstractRequest
{
    private const EMAIL = RequestArgumentsEnum::EMAIL;
    private const PHONE = RequestArgumentsEnum::PHONE;
    private const NAME  = RequestArgumentsEnum::NAME;
    private const TEXT  = RequestArgumentsEnum::TEXT;

    public function attributes(): array
    {
        return [
            self::TEXT => 'Детали платежа',
        ];
    }

    public function rules(): array
    {
        return [
            self::EMAIL => [
                'nullable',
                'string',
            ],
            self::PHONE => [
                'nullable',
                'string',
            ],
            self::NAME  => [
                'nullable',
                'string',
            ],
            self::TEXT  => [
                'required',
                'string',
                'min:1',
            ],
        ];
    }

    public function getFullText(): string
    {
        return sprintf("%s%s%s%s%s",
            $this->getFormattedName(),
            $this->getFormattedEmail(),
            $this->getFormattedPhone(),
            $this->getFormattedAttachmentsList(),
            $this->getFormattedText(),
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

    public function getText(): string
    {
        return (string) $this->getStringOrNull(self::TEXT);
    }

    private function getFormattedText(): string
    {
        return sprintf("Текст обращения:\n%s", $this->getText());
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
