<?php declare(strict_types=1);

namespace Core\Domains\Files\Collections;

use Core\Collections\CollectionTrait;
use Core\Domains\Files\Models\FolderDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, FolderDTO>
 */
class FolderCollection extends Collection
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof FolderDTO;
    }
}
