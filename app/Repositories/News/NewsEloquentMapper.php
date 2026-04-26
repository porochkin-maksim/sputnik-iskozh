<?php declare(strict_types=1);

namespace App\Repositories\News;

use App\Locators\FileLocator;
use App\Models\News;
use Carbon\Carbon;
use Core\Domains\News\NewsCategoryEnum;
use Core\Domains\News\NewsCollection;
use Core\Domains\News\NewsEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use IteratorAggregate;

readonly class NewsEloquentMapper implements RepositoryDataMapperInterface
{
    /**
     * @var NewsEntity $entity
     * @var News|null  $data
     */
    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        if ($data) {
            $result = $data;
        }
        else {
            $result = News::make();
        }

        $publishedAt = $entity->getPublishedAt()?->format(DateTimeFormat::DATE_TIME_DEFAULT)
            ? : Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT);

        return $result->fill([
            News::CATEGORY     => $entity->getCategory()->value,
            News::TITLE        => $entity->getTitle(),
            News::DESCRIPTION  => $entity->getDescription(),
            News::ARTICLE      => $entity->getArticle(),
            News::IS_LOCK      => $entity->isLock(),
            News::PUBLISHED_AT => $publishedAt,
        ]);
    }

    /**
     * @var News $data
     */
    public function makeEntityFromRepositoryData($data): object
    {
        $result = new NewsEntity()
            ->setId($data->{$data::ID})
            ->setTitle($data->{$data::TITLE})
            ->setDescription($data->{$data::DESCRIPTION})
            ->setArticle($data->{$data::ARTICLE})
            ->setIsLock($data->{$data::IS_LOCK})
            ->setPublishedAt($data->{$data::PUBLISHED_AT})
            ->setCategory(NewsCategoryEnum::tryFrom($data->{$data::CATEGORY}))
            ->setCreatedAt($data->{$data::CREATED_AT})
            ->setUpdatedAt($data->{$data::UPDATED_AT})
        ;

        if (isset($data->getRelations()[News::RELATION_FILES])) {
            $result->setFiles(FileLocator::FileFactory()->makeEntityFromRepositoryDatas($data->getRelation(News::RELATION_FILES)));
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $collection = new NewsCollection();

        foreach ($datas as $data) {
            $collection->add($this->makeEntityFromRepositoryData($data));
        }

        return $collection;
    }
}
