<?php declare(strict_types=1);

namespace Core\Domains\News\Models;

use App\Models\News;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\News\Enums\CategoryEnum;

class NewsSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setWithFiles(): static
    {
        $this->with[] = News::FILES;

        return $this;
    }

    public function setCategory(CategoryEnum $category): static
    {
        $this->addWhere(News::CATEGORY, SearcherInterface::EQUALS, $category->value);

        return $this;
    }
}
