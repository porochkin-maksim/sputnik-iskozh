<?php declare(strict_types=1);

namespace Core\Objects\News\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Objects\News\Models\NewsDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, NewsDTO>
 */
class NewsCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof NewsDTO;
    }
}
