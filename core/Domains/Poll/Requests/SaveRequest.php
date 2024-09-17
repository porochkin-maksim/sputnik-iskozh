<?php declare(strict_types=1);

namespace Core\Domains\Poll\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Requests\RequestArgumentsEnum;

class SaveRequest extends AbstractRequest
{
    private const ID          = RequestArgumentsEnum::ID;
    private const TITLE       = RequestArgumentsEnum::TITLE;
    private const DESCRIPTION = RequestArgumentsEnum::DESCRIPTION;
    private const START_AT    = RequestArgumentsEnum::START_AT;
    private const END_AT      = RequestArgumentsEnum::END_AT;

    public function rules(): array
    {
        return [
            self::TITLE => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::TITLE . '.required' => 'Укажите название опроса',
        ];
    }

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getTitle(): string
    {
        return $this->getString(self::TITLE);
    }

    public function getDescription(): string
    {
        return $this->getString(self::DESCRIPTION);
    }

    public function getstartAt(): ?string
    {
        return $this->get(self::START_AT);
    }

    public function getEndsAt(): ?string
    {
        return $this->get(self::END_AT);
    }
}
