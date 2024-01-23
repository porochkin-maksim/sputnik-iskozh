<?php

namespace Core\Cache;

use App\Models\User;

enum CachePrefixEnum: string
{
    case User = User::class;

    public function md5(): string
    {
        return md5($this->value);
    }
}
