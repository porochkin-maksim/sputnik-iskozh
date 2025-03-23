<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Collections;

use Cknow\Money\Money;
use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Billing\Payment\Models\PaymentDTO;
use Core\Services\Money\MoneyService;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, PaymentDTO>
 */
class PaymentCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof PaymentDTO;
    }

    public function getTotalCost(): float
    {
        $result = 0;

        foreach ($this as $payment) {
            $result += $payment->getCost();
        }

        return $result;
    }

    public function getVerified(): static
    {
        return $this->filter(function (PaymentDTO $payment) {
            return $payment->isVerified();
        });
    }

    public function getTotalCostMoney(): Money
    {
        return MoneyService::parse($this->getTotalCost());
    }
}
