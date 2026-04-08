<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Collection;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, TicketCategoryDTO>
 */
class TicketCategoryCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TicketCategoryDTO;
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
