<?php declare(strict_types=1);

namespace Core\Domains\Proposal\Requests;

use App\Http\Requests\AbstractRequest;

class CreateRequest extends AbstractRequest
{
    private const EMAIL = 'email';
    private const PHONE = 'phone';
    private const NAME  = 'name';
    private const TEXT  = 'text';

    public function attributes(): array
    {
        return [
            self::TEXT => 'Предложение',
        ];
    }

    public function rules(): array
    {
        return [
            self::EMAIL => [
                'nullable',
                'email',
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
        return sprintf("%s%s%s%s", $this->getName(), $this->getEmail(), $this->getPhone(), $this->getText());
    }

    private function getText(): string
    {
        $text = $this->get(self::TEXT);

        return sprintf("Обращение:\n%s", $text);
    }

    private function getEmail(): string
    {
        $email = $this->get(self::EMAIL);

        return $email ? sprintf("Почта для связи: %s\n", $email) : '';
    }

    private function getName(): string
    {
        $name = $this->get(self::NAME);

        return $name ? sprintf("Обращение от: %s\n", $name) : '';
    }

    private function getPhone(): string
    {
        $phone = $this->get(self::PHONE);

        return $phone ? sprintf("Телефон для связи: %s\n", $phone) : '';
    }
}
