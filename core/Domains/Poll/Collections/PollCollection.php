<?php declare(strict_types=1);

namespace Core\Domains\Poll\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Poll\Models\PollDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, PollDTO>
 */
class PollCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof PollDTO;
    }
}
