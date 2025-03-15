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
}
