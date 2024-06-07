<?php declare(strict_types=1);

namespace Core\Objects\File\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Requests\RequestArgumentsEnum;

class SaveRequest extends AbstractRequest
{
    private const ID   = RequestArgumentsEnum::ID;
    private const NAME = RequestArgumentsEnum::NAME;

    public function rules(): array
    {
        return [
            self::NAME => [
                'required',
                'string',
                'min:3',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NAME . '.required' => '',
        ];
    }

    public function getId(): int
    {
        return $this->get(self::ID);
    }

    public function getName(): string
    {
        return $this->get(self::NAME);
    }
}
