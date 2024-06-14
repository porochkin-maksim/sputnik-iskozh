<?php declare(strict_types=1);

namespace Core\Domains\News\Factories;

use App\Models\News;
use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Core\Domains\File\Factories\FileFactory;
use Core\Domains\News\Models\NewsDTO;

readonly class NewsFactory
{
    public function __construct(
        private FileFactory $fileFactory,
    )
    {
    }

    public function makeDefault(): NewsDTO
    {
        $result = new NewsDTO();

        return $result
            ->setId(null)
            ->setTitle('')
            ->setArticle('');
    }

    public function makeModelFromDto(NewsDTO $dto, ?News $news = null): News
    {
        if ($news) {
            $result = $news;
        }
        else {
            $result = News::make();
        }

        $publishedAt = $dto->getPublishedAt()
            ? $dto->getPublishedAt()->format(DateTimeFormat::DATE_TIME_DEFAULT)
            : Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT);

        return $result->fill([
            News::TITLE        => $dto->getTitle(),
            News::ARTICLE      => $dto->getArticle(),
            News::PUBLISHED_AT => $publishedAt,
        ]);
    }

    public function makeDtoFromObject(News $news): NewsDTO
    {
        $result = new NewsDTO();

        $result
            ->setId($news->id)
            ->setTitle($news->title)
            ->setArticle($news->article)
            ->setPublishedAt($news->published_at)
            ->setCreatedAt($news->created_at)
            ->setUpdatedAt($news->updated_at);

        if ($news->{News::FILES}) {
            $result->setFiles($this->fileFactory->makeDtoFromObjects($news->{News::FILES}));
        }

        return $result;
    }
}