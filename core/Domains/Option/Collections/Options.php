<?php declare(strict_types=1);

namespace Core\Domains\Option\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Option\Models\OptionDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, OptionDTO>
 */
class Options extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof OptionDTO;
    }
}
