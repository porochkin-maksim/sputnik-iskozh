<?php declare(strict_types=1);

namespace App\Http\Resources\Public\News;

use Core\Domains\News\Models\NewsDTO;
use Core\Enums\DateTimeFormat;
use JsonSerializable;

class IndexNewsListResource implements JsonSerializable
{
    /**
     * @param NewsDTO[] $news
     */
    public function __construct(private array $news) { }

    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->news as $news) {
            $result[] = [
                'id'          => $news->getId(),
                'title'       => $news->getTitle(),
                'publishedAt' => $news->getPublishedAt()->format(DateTimeFormat::DATE_VIEW_FORMAT),
                'url'         => $news->getUrl(),
            ];
        }

        return $result;
    }
}