<?php declare(strict_types=1);

namespace Core\Domains\Option;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, OptionEntity>
 */
class OptionCollection extends Collection
{
    use CollectionTrait;
}
