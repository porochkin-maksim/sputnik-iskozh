<?php declare(strict_types=1);

namespace App\Http\Resources\Public;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Shared\Files\FileResource;
use App\Http\Resources\Shared\ResourseList;
use App\Locators\NewsLocator;
use Core\Domains\News\NewsEntity;
use Core\Shared\Helpers\DateTime\DateTimeFormat;

readonly class NewsResource extends AbstractResource
{
    public function __construct(
        private NewsEntity $entity,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'dossier'     => [
                'createdAt'   => $this->entity->getCreatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
                'publishedAt' => $this->entity->getPublishedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
            ],
            'id'          => $this->entity->getId(),
            'title'       => $this->entity->getTitle(),
            'description' => $this->entity->getDescription(),
            'article'     => $this->entity->getArticle(),
            'category'    => $this->entity->getCategory()->value,
            'files'       => new ResourseList($this->entity->getFiles(), FileResource::class),
            'isLock'      => $this->entity->isLock(),
            'publishedAt' => $this->entity->getPublishedAt()?->format(DateTimeFormat::DATE_TIME_MAIN),
            'url'         => NewsLocator::UrlFactory()->makeUrl($this->entity),
        ];
    }
}