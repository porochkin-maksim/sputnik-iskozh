<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, CounterHistoryEntity>
 */
class CounterHistoryCollection extends Collection
{
    use CollectionTrait;

    public function hasUnlinked(): bool
    {
        foreach ($this as $item) {
            if ( ! $item->getCounterId()) {
                return true;
            }
        }
        return false;
    }

    public function getUnlinked(): static
    {
        $result = new static();
        foreach ($this as $item) {
            if ( ! $item->getCounterId()) {
                $result->add($item);
            }
        }
        return $result;
    }

    public function sortById(): static
    {
        $items = $this->toArray();
        usort($items, static fn(CounterHistoryEntity $a, CounterHistoryEntity $b) => $a->getId() <= $b->getId() ? -1 : 1);
        return new static($items);
    }
}
