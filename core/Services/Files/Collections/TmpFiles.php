<?php declare(strict_types=1);

namespace Core\Services\Files\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Services\Files\Models\TmpFile;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, TmpFile>
 */
class TmpFiles extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TmpFile;
    }
}
