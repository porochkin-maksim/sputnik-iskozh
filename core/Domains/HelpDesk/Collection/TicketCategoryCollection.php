<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Collection;

use Core\Shared\Collections\CollectionTrait;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Shared\Collections\Collection;;

/**
 * @template-extends Collection<int, TicketCategoryEntity>
 */
class TicketCategoryCollection extends Collection
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TicketCategoryEntity;
    }

    public function hasServices(): bool
    {
        foreach ($this->items as $item) {
            if ($item->getServices()?->count()) {
                return true;
            }
        }

        return false;
    }
}
