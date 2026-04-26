<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Collection;

use Core\Collections\CollectionTrait;
use Core\Domains\HelpDesk\Models\TicketDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, TicketDTO>
 */
class TicketCollection extends Collection
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TicketDTO;
    }
}
