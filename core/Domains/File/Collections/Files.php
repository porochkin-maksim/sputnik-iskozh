<?php declare(strict_types=1);

namespace Core\Domains\File\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\File\Models\FileDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, FileDTO>
 */
class Files extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof FileDTO;
    }
}
