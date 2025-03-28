<?php declare(strict_types=1);

namespace Core\Services\Money;

use Cknow\Money\Money;
use NumberFormatter;

/**
 * https://github.com/cknow/laravel-money
 */
class MoneyService
{
    public static function parse(mixed $amount): Money
    {
        try {
            $amount = (float) (string) $amount;
        }
        catch (\Exception) {
            $amount = 0;
        }

        return Money::parse($amount, 'RUB');
    }

    public static function toFloat(Money $money): float
    {
        return (float) $money->formatByDecimal();
    }
}
