<?php declare(strict_types=1);

namespace App\Locators\News;

use Core\Domains\News\NewsCategoryEnum;
use Core\Domains\News\NewsEntity;
use App\Resources\RouteNames;

class UrlFactory
{
    public function makeUrl(NewsEntity $news): ?string
    {
        if ( ! $news->getId()) {
            return null;
        }

        return match ($news->getCategory()?->value) {
            NewsCategoryEnum::DEFAULT->value      => route(RouteNames::NEWS_SHOW, $news->getId()),
            NewsCategoryEnum::ANNOUNCEMENT->value => route(RouteNames::ANNOUNCEMENTS_SHOW, $news->getId()),
            default                               => null,
        };
    }
}
