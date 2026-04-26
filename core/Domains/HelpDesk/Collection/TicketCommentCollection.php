<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Collection;

use Core\Shared\Collections\CollectionTrait;
use Core\Domains\HelpDesk\Models\TicketCommentEntity;
use Core\Shared\Collections\Collection;;

/**
 * @template-extends Collection<int, TicketCommentEntity>
 */
class TicketCommentCollection extends Collection
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TicketCommentEntity;
    }
}
