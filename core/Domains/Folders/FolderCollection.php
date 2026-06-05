<?php declare(strict_types=1);

namespace Core\Domains\Folders;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, FolderEntity>
 */
class FolderCollection extends Collection
{
    use CollectionTrait;
}
