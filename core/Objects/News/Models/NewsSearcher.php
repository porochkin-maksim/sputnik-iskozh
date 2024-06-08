<?php declare(strict_types=1);

namespace Core\Objects\News\Models;

use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class NewsSearcher implements SearcherInterface
{
    use SearcherTrait;

    private bool $withFiles = false;

    public function setWithFiles(): static
    {
        $this->with[] = 'files';

        return $this;
    }
}
