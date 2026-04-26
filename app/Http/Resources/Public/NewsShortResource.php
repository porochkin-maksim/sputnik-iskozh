<?php declare(strict_types=1);

namespace App\Http\Resources\Public;

use App\Locators\NewsLocator;
use Core\Domains\News\NewsEntity;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use JsonSerializable;

readonly class NewsShortResource implements JsonSerializable
{
    public function __construct(
        private NewsEntity $entity,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->entity->getId(),
            'title'       => $this->entity->getTitle(),
            'publishedAt' => $this->entity->getPublishedAt()?->format(DateTimeFormat::DATE_VIEW_FORMAT),
            'url'         => NewsLocator::UrlFactory()->makeUrl($this->entity),
        ];
    }
}
