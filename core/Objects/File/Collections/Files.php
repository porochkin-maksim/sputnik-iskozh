<?php declare(strict_types=1);

namespace Core\Objects\File\Collections;

use App\Models\File;
use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Illuminate\Support\Collection;

class Files extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof File;
    }
}
