<?php declare(strict_types=1);

namespace Core\Domains\Account\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Account\Enums\AccountIdEnum;
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

    public function sortDefault(): static
    {
        return $this->sort(function (AccountDTO $account1, AccountDTO $account2) {
            if ($account1->getId() === AccountIdEnum::SNT->value) {
                return -1;
            }
            if ($account2->getId() === AccountIdEnum::SNT->value) {
                return 1;
            }

            return 0;
        });
    }
}
