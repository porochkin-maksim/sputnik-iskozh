<?php declare(strict_types=1);

namespace Core\Domains\News;

use App\Models\News;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class NewsSearcher extends BaseSearcher
{
    public function setWithFiles(): static
    {
        $this->with[] = News::RELATION_FILES;

        return $this;
    }

    public function setCategory(NewsCategoryEnum $category): static
    {
        $this->addWhere(News::CATEGORY, SearcherInterface::EQUALS, $category->value);

        return $this;
    }
}
