<?php declare(strict_types=1);

namespace Core\Domains\Counter\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Counter\Models\CounterDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, CounterDTO>
 */
class CounterCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof CounterDTO;
    }

    public function getInvoicing(): static
    {
        $result = new static();
        foreach ($this->items as $counter) {
            if ($counter->isInvoicing()) {
                $result->add($counter);
            }
        }

        return $result;
    }
}
