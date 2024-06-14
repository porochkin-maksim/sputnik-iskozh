<?php declare(strict_types=1);

namespace Core\Domains\User\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\User\Models\UserDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, UserDTO>
 */
class Users extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof UserDTO;
    }

    public function getById(?int $id): ?UserDTO
    {
        foreach ($this as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }

        return null;
    }
}
