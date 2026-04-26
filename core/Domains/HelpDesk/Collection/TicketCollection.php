<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Collection;

use Core\Shared\Collections\CollectionTrait;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Shared\Collections\Collection;;

/**
 * @template-extends Collection<int, TicketEntity>
 */
class TicketCollection extends Collection
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TicketEntity;
    }
}
