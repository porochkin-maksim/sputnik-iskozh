<?php declare(strict_types=1);

namespace Core\Domains\Account\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Account\Models\AccountDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, AccountDTO>
 */
class AccountCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof AccountDTO;
    }
}
