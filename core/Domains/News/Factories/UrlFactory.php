<?php declare(strict_types=1);

namespace Core\Domains\News\Factories;

use Core\Domains\News\Enums\CategoryEnum;
use Core\Domains\News\Models\NewsDTO;
use Core\Resources\RouteNames;

class UrlFactory
{
    public function makeUrl(NewsDTO $news): ?string
    {
        if (!$news->getId()) {
            return null;
        }

        return match ($news->getCategory()?->value) {
            CategoryEnum::DEFAULT->value => route(RouteNames::NEWS_SHOW, $news->getId()),
            CategoryEnum::ANNOUNCEMENT->value => route(RouteNames::ANNOUNCEMENTS_SHOW, $news->getId()),
            default => null,
        };
    }
}
