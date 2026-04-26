<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Collection;

use Core\Collections\CollectionTrait;
use Core\Domains\HelpDesk\Models\TicketCommentDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, TicketCommentDTO>
 */
class TicketCommentCollection extends Collection
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TicketCommentDTO;
    }
}
