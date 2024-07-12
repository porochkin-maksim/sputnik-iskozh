<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;
use Core\Requests\RequestArgumentsEnum;

class ChangeOrderRequest extends AbstractRequest
{
    private const INDEX = RequestArgumentsEnum::INDEX;

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
