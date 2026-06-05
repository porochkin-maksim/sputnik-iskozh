<?php declare(strict_types=1);

namespace App\Repositories\News;

use App\Models\News;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Repositories\RepositoryConfig;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\News\NewsCollection;
use Core\Domains\News\NewsEntity;
use Core\Domains\News\NewsRepositoryInterface;
use Core\Domains\News\NewsSearcher;
use Core\Domains\News\NewsSearchResponse;
use Core\Repositories\SearcherInterface;

class NewsEloquentRepository implements NewsRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly NewsEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : News::class,
            table              : News::TABLE,
            collectionClass    : NewsCollection::class,
            searchResponseClass: NewsSearchResponse::class,
            searcherClass      : NewsSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): NewsSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(NewsEntity $news): NewsEntity
    {
        /** @var News|null $model */
        $model = $this->getModelById($news->getId());
        $model = $this->mapper->makeRepositoryDataFromEntity($news, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?NewsEntity
    {
        /** @var News|null $model */
        $searcher = $this->getEmptySearcher()
            ->setWithFiles()
        ;
        $model    = $this->getModelById($id, $searcher);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): NewsSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    /**
     * @return int[]
     */
    public function getIdsByFullTextSearch(string $search): array
    {
        return News::select(News::ID)->whereRaw(
            sprintf(
                'MATCH(%s) AGAINST(? IN BOOLEAN MODE)',
                implode(',', [News::TITLE, News::DESCRIPTION, News::ARTICLE]),
            ),
            $search,
        )->get()->map(function (News $news) {
            return $news->id;
        })->unique()->toArray();
    }
}
