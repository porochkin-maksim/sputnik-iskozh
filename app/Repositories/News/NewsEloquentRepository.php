<?php declare(strict_types=1);

namespace App\Repositories\News;

use App\Models\News;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Shared\Collections\Collection;
use Core\Domains\News\NewsCollection;
use Core\Domains\News\NewsEntity;
use Core\Domains\News\NewsRepositoryInterface;
use Core\Domains\News\NewsSearcher;
use Core\Domains\News\NewsSearchResponse;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use ReturnTypeWillChange;

class NewsEloquentRepository implements NewsRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly NewsEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return News::class;
    }

    protected function getTable(): string
    {
        return News::TABLE;
    }

    protected function getEmptyCollection(): Collection
    {
        return new NewsCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return NewsSearchResponse
     */
    protected function getEmptySearchResponse(): NewsSearchResponse
    {
        return new NewsSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return NewsSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new NewsSearcher();
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
            ->setWithDeleted()
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
