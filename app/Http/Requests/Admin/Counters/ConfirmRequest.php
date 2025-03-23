<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Counters;

use App\Http\Requests\AbstractRequest;

class ConfirmRequest extends AbstractRequest
{
    private const IDS = 'ids';

    public function rules(): array
    {
        return [
            self::IDS => [
                'required',
                'array',
            ],
        ];
    }

    public function getIds(): array
    {
        return array_map('intval', $this->getArray(self::IDS));
    }
}
