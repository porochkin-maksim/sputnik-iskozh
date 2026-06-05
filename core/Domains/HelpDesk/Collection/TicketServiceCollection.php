<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Collection;

use Core\Shared\Collections\CollectionTrait;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Shared\Collections\Collection;;

/**
 * @template-extends Collection<int, TicketServiceEntity>
 */
class TicketServiceCollection extends Collection
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TicketServiceEntity;
    }

    public function getActive(): static
    {
        $result = new static();

        foreach ($this->items as $item) {
            if ($item->getIsActive()) {
                $result->add($item);
            }
        }

        return $result;
    }
}
