<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, TransactionEntity>
 */
class TransactionCollection extends Collection
{
    use CollectionTrait;

    public function getTotalCost(): float
    {
        $result = 0.0;

        foreach ($this as $transaction) {
            $result += (float) $transaction->getCost();
        }

        return $result;
    }

    public function getByClaimId(?int $claimId): static
    {
        return $this->filter(static fn(TransactionEntity $t) => $t->getClaimId() === $claimId);
    }
}
