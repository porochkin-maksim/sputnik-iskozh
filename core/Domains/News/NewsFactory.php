<?php declare(strict_types=1);

namespace Core\Domains\News;

readonly class NewsFactory
{
    public function makeDefault(): NewsEntity
    {
        return new NewsEntity()
            ->setId(null)
            ->setTitle('')
            ->setCategory(NewsCategoryEnum::DEFAULT)
            ->setArticle('')
            ->setIsLock(false);
    }
}
