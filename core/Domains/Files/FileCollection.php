<?php declare(strict_types=1);

namespace Core\Domains\Files;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, FileEntity>
 */
class FileCollection extends Collection
{
    use CollectionTrait;

    public function getImages(): static
    {
        return $this->filter(fn(FileEntity $entity) => $entity->isImage());
    }
}
