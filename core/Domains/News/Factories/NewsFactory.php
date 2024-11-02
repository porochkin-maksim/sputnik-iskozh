<?php declare(strict_types=1);

namespace Core\Domains\News\Factories;

use App\Models\News;
use Carbon\Carbon;
use Core\Domains\News\Enums\CategoryEnum;
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
            ->setCategory(CategoryEnum::DEFAULT)
            ->setArticle('')
            ->setIsLock(false);
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
            News::CATEGORY     => $dto->getCategory()->value,
            News::TITLE        => $dto->getTitle(),
            News::DESCRIPTION  => $dto->getDescription(),
            News::ARTICLE      => $dto->getArticle(),
            News::IS_LOCK      => $dto->isLock(),
            News::PUBLISHED_AT => $publishedAt,
        ]);
    }

    public function makeDtoFromObject(News $news): NewsDTO
    {
        $result = new NewsDTO();

        $result
            ->setId($news->id)
            ->setTitle($news->title)
            ->setDescription($news->description)
            ->setArticle($news->article)
            ->setIsLock($news->is_lock)
            ->setPublishedAt($news->published_at)
            ->setCategory(CategoryEnum::tryFrom($news->category))
            ->setCreatedAt($news->created_at)
            ->setUpdatedAt($news->updated_at);

        if (isset($news->getRelations()[News::FILES])) {
            $result->setFiles($this->fileFactory->makeDtoFromObjects($news->getRelation(News::FILES)));
        }

        return $result;
    }
}