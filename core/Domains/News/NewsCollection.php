<?php declare(strict_types=1);

namespace Core\Domains\News;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, NewsEntity>
 */
class NewsCollection extends Collection
{
    use CollectionTrait;
}
