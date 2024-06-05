<?php declare(strict_types=1);

namespace Core\Objects\User\Collections;

use App\Models\User;
use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, User>
 */
class Users extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof User;
    }
}
