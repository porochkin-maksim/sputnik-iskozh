<?php declare(strict_types=1);

namespace Core\Objects\Account\Collections;

use App\Models\Account\Account;
use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, Account>
 */
class Accounts extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof Account;
    }
}
