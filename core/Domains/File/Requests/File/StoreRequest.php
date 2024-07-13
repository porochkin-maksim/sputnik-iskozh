<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;
use Core\Requests\RequestArgumentsEnum;

class StoreRequest extends AbstractRequest
{
    private const PARENT_ID = RequestArgumentsEnum::PARENT_ID;

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

    public function getParentId(): ?int
    {
        return $this->getIntOrNull(self::PARENT_ID);
    }
}
