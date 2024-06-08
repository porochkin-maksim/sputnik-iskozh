<?php declare(strict_types=1);

namespace Core\Objects\News\Collections;

use App\Models\News;
use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, News>
 */
class NewsCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof News;
    }
}
