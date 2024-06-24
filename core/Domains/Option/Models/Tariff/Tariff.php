<?php declare(strict_types=1);

namespace Core\Domains\Option\Models\Tariff;

use Cknow\Money\Money;
use Core\Domains\Option\Models\OptionDTO;
use Core\Services\Money\MoneyService;

class Tariff extends OptionDTO
{
    public function getMoney(): Money
    {
        return MoneyService::parse($this->getData());
    }
}
