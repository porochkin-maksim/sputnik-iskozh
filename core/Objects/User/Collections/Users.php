<?php declare(strict_types=1);

namespace Core\Objects\User\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Objects\User\Models\UserDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, UserDTO>
 */
class Users extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof User;
    }
}
