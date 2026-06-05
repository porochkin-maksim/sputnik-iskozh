<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use App\Services\Money\MoneyService;
use Cknow\Money\Money;
use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, PaymentEntity>
 */
class PaymentCollection extends Collection
{
    use CollectionTrait;

    public function getTotalCost(): float
    {
        $result = 0.0;

        foreach ($this as $payment) {
            $result += (float) $payment->getCost();
        }

        return $result;
    }

    public function getVerified(): static
    {
        return $this->filter(static fn(PaymentEntity $payment) => $payment->isVerified());
    }

    public function getTotalCostMoney(): Money
    {
        return MoneyService::parse($this->getTotalCost());
    }
}
