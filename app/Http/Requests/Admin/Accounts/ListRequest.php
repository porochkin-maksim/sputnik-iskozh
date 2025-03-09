<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Accounts;

use App\Http\Requests\DefaultRequest;
use Core\Requests\RequestArgumentsEnum;

class ListRequest extends DefaultRequest
{
    private const ACCOUNT_ID = RequestArgumentsEnum::ACCOUNT_ID;

    public function getAccountId(): ?int
    {
        return $this->getIntOrNull(self::ACCOUNT_ID);
    }
}
