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
        return (new NewsDTO())
            ->setId(null)
            ->setTitle('')
            ->setCategory(CategoryEnum::DEFAULT)
            ->setArticle('')
            ->setIsLock(false);
    }

    public function makeModelFromDto(NewsDTO $dto, ?News $model = null): News
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = News::make();
        }

        $publishedAt = $dto->getPublishedAt()?->format(DateTimeFormat::DATE_TIME_DEFAULT)
            ?: Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT);

        return $result->fill([
            News::CATEGORY     => $dto->getCategory()->value,
            News::TITLE        => $dto->getTitle(),
            News::DESCRIPTION  => $dto->getDescription(),
            News::ARTICLE      => $dto->getArticle(),
            News::IS_LOCK      => $dto->isLock(),
            News::PUBLISHED_AT => $publishedAt,
        ]);
    }

    public function makeDtoFromObject(News $model): NewsDTO
    {
        $result = new NewsDTO();

        $result
            ->setId($model->id)
            ->setTitle($model->title)
            ->setDescription($model->description)
            ->setArticle($model->article)
            ->setIsLock($model->is_lock)
            ->setPublishedAt($model->published_at)
            ->setCategory(CategoryEnum::tryFrom($model->category))
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        if (isset($model->getRelations()[News::FILES])) {
            $result->setFiles($this->fileFactory->makeDtoFromObjects($model->getRelation(News::FILES)));
        }

        return $result;
    }
}