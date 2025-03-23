<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Infra\HistoryChanges\Models\HistoryChangesDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, HistoryChangesDTO>
 */
class HistoryChangesCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof HistoryChangesDTO;
    }
}
