<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Collection;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, TicketServiceDTO>
 */
class TicketServiceCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TicketServiceDTO;
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
