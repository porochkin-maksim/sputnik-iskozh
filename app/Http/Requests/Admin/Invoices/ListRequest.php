<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Invoices;

use App\Http\Requests\DefaultRequest;
use Core\Requests\RequestArgumentsEnum;

class ListRequest extends DefaultRequest
{
    private const TYPE       = RequestArgumentsEnum::TYPE;
    private const PERIOD_ID  = RequestArgumentsEnum::PERIOD_ID;
    private const ACCOUNT_ID = RequestArgumentsEnum::ACCOUNT_ID;

    public function getType(): ?int
    {
        return $this->getIntOrNull(self::TYPE);
    }

    public function getPeriodId(): ?int
    {
        return $this->getIntOrNull(self::PERIOD_ID);
    }

    public function getAccountId(): ?int
    {
        return $this->getIntOrNull(self::ACCOUNT_ID);
    }
}
