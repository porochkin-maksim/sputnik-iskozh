<?php declare(strict_types=1);

namespace Core\Domains\Access\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Access\Models\RoleDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, RoleDTO>
 */
class RoleCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof RoleDTO;
    }
}
