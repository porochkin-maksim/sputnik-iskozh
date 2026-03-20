<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;

class SaveRequest extends AbstractRequest
{
    private const string ID   = 'id';
    private const string NAME = 'name';

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
