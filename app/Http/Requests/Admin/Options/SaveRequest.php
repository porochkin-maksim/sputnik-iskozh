<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Options;

use App\Http\Requests\AbstractRequest;
use Core\Requests\RequestArgumentsEnum;

class SaveRequest extends AbstractRequest
{
    private const ID   = RequestArgumentsEnum::ID;
    private const DATA = RequestArgumentsEnum::DATA;

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
