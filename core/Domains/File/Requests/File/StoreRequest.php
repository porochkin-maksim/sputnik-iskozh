<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;

class StoreRequest extends AbstractRequest
{
    private const string PARENT_ID = 'parent_id';

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
