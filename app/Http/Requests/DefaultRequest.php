<?php

namespace App\Http\Requests;

use Core\Requests\RequestArgumentsEnum;

class DefaultRequest extends AbstractRequest
{
    private const string LIMIT  = RequestArgumentsEnum::LIMIT;
    private const string SKIP   = RequestArgumentsEnum::SKIP;
    private const string SEARCH = RequestArgumentsEnum::SEARCH;

    public function getLimit(): ?int
    {
        return $this->getIntOrNull(self::LIMIT);
    }

    public function getOffset(): ?int
    {
        return $this->getIntOrNull(self::SKIP);
    }

    public function getSearch(): ?string
    {
        return $this->getStringOrNull(self::SEARCH);
    }

    public function getLastId(): ?int
    {
        return $this->getIntOrNull(RequestArgumentsEnum::LAST_ID);
    }
}
