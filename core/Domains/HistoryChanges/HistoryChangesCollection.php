<?php declare(strict_types=1);

namespace Core\Domains\HistoryChanges;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, HistoryChangesEntity>
 */
class HistoryChangesCollection extends Collection
{
    use CollectionTrait;
}
