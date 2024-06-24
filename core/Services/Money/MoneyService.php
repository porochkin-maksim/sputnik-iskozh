<?php declare(strict_types=1);

namespace Core\Services\Money;

use Cknow\Money\Money;

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
}
