<?php declare(strict_types=1);

namespace Core\Domains\Counter;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, CounterEntity>
 */
class CounterCollection extends Collection
{
    use CollectionTrait;

    public function getInvoicing(): static
    {
        $result = new static();
        foreach ($this->toArray() as $counter) {
            if ($counter->isInvoicing()) {
                $result->add($counter);
            }
        }
        return $result;
    }
}
