<?php declare(strict_types=1);

namespace Core\Objects\Report\Collections;

use App\Models\Report;
use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, Report>
 */
class Reports extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof Report;
    }
}
