<?php declare(strict_types=1);

namespace Core\Db\Searcher\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Db\Searcher\Models\Where;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, Where>
 */
class WhereCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof Where;
    }
}
