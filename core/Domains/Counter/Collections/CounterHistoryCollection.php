<?php declare(strict_types=1);

namespace Core\Domains\Counter\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, CounterHistoryDTO>
 */
class CounterHistoryCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof CounterHistoryDTO;
    }

    public function hasUnlinked(): bool
    {
        foreach ($this as $item) {
            if (!$item->getCounterId()) {
                return true;
            }
        }

        return false;
    }

    public function getUnlinked(): static
    {
        $result = new static();

        foreach ($this as $item) {
            if (!$item->getCounterId()) {
                $result->add($item);
            }
        }

        return $result;
    }

    public function sortById(): static
    {
        return $this->sort(function (CounterHistoryDTO $account1, CounterHistoryDTO $account2) {
            return $account1->getId() <= $account2->getId() ? -1 : 1;
        });
    }

    public function getById(?int $id): ?CounterHistoryDTO
    {
        foreach ($this as $item) {
            if ($item->getId() === $id) {
                return $item;
            }
        }

        return null;
    }
}
