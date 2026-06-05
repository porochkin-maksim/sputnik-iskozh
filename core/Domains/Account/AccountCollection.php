<?php declare(strict_types=1);

namespace Core\Domains\Account;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, AccountEntity>
 */
class AccountCollection extends Collection
{
    use CollectionTrait;

    public function sortDefault(): static
    {
        $items = $this->toArray();
        usort($items, static function (AccountEntity $account1, AccountEntity $account2): int {
            if ($account1->isSnt()) {
                return -1;
            }
            if ($account2->isSnt()) {
                return 1;
            }
            return 0;
        });

        return new static($items);
    }

    public function hasSeveral(): bool
    {
        return $this->count() > 1;
    }

    public function searchById(?int $accountId): ?AccountEntity
    {
        return $this->find(static fn(AccountEntity $account) => $account->getId() === $accountId);
    }

    public function getNumbers(): array
    {
        $result = [];
        foreach ($this->toArray() as $account) {
            $result[] = $account->getNumber();
        }
        return $result;
    }

    public function getNumbersWitchFraction(): array
    {
        $result = [];
        foreach ($this->toArray() as $account) {
            $result[] = $account->getNumber() . ($account->getFractionPercent() ? " ({$account->getFractionPercent()})" : null);
        }
        return $result;
    }
}
