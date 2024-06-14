<?php declare(strict_types=1);

namespace Core\Domains\Access\Collections;

use App\Models\Access\Role;
use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, Role>
 */
class Roles extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof Role;
    }
}
