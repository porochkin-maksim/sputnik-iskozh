<?php

namespace App\Http\Requests;

use Core\Requests\RequestArgumentsEnum;

class DefaultRequest extends AbstractRequest
{
    private const LIMIT  = RequestArgumentsEnum::LIMIT;
    private const SKIP   = RequestArgumentsEnum::SKIP;
    private const SEARCH = RequestArgumentsEnum::SEARCH;

    public function getLimit(): ?int
    {
        return $this->getIntOrNull(self::LIMIT);
    }

    public function getOffset(): ?int
    {
        return $this->getIntOrNull(self::SKIP);
    }
}
