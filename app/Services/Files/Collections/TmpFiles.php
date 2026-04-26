<?php declare(strict_types=1);

namespace App\Services\Files\Collections;

use Core\Shared\Collections\CollectionTrait;
use App\Services\Files\Models\TmpFile;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, TmpFile>
 */
class TmpFiles extends Collection
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TmpFile;
    }
}
