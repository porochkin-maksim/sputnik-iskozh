<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Options;

use App\Http\Requests\AbstractRequest;

class SaveRequest extends AbstractRequest
{
    private const string ID   = 'id';
    private const string DATA = 'data';

    public function rules(): array
    {
        return [
            self::ID   => ['required', 'integer'],
            self::DATA => ['required', 'array'],
        ];
    }

    public function getId(): int
    {
        return $this->getInt(self::ID);
    }

    public function getData(): array
    {
        return $this->getArray(self::DATA);
    }
}
