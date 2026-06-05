<?php declare(strict_types=1);

namespace Core\Domains\Access;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, RoleEntity>
 */
class RoleCollection extends Collection
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof RoleEntity;
    }
}
