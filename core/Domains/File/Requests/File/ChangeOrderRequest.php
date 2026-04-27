<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;

class ChangeOrderRequest extends AbstractRequest
{
    private const string INDEX = 'index';

    public function rules(): array
    {
        return [

        ];
    }

    public function messages(): array
    {
        return [

        ];
    }

    public function getIndex(): int
    {
        return $this->getInt(self::INDEX);
    }
}
