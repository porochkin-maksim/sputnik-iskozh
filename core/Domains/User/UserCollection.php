<?php declare(strict_types=1);

namespace Core\Domains\User;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, UserEntity>
 */
class UserCollection extends Collection
{
    use CollectionTrait;
}
