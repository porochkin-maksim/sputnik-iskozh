<?php declare(strict_types=1);

namespace Core\Domains\News\Models;

use App\Models\News;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class NewsSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setWithFiles(): static
    {
        $this->with[] = News::FILES;

        return $this;
    }
}
