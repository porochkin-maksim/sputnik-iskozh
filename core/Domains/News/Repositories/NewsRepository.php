<?php declare(strict_types=1);

namespace Core\Domains\News\Repositories;

use App\Models\News;
use Core\Db\RepositoryTrait;
use Core\Domains\News\Collections\NewsCollection;
use Core\Domains\News\Models\NewsSearcher;

class NewsRepository
{
    private const TABLE = News::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return News::class;
    }

    public function getById(?int $id): ?News
    {
        /** @var ?News $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function getByIds(array $ids): NewsCollection
    {
        return new NewsCollection($this->traitGetByIds($ids));
    }

    public function save(News $news): News
    {
        $news->save();

        return $news;
    }

    public function unlockNews(): void
    {
        News::where(News::IS_LOCK, true)->update([News::IS_LOCK => false]);
    }

    /**
     * @return int[]
     */
    public function getIdsByFullTextSearch(string $search): array
    {
        return News::select('id')->whereRaw(
            sprintf("MATCH(%s) AGAINST(? IN BOOLEAN MODE)",
                implode(',', [News::TITLE, News::DESCRIPTION, News::ARTICLE]),
            ),
            $search
        )->get()->map(function (News $news) {
            return $news->id;
        })->unique()->toArray();
    }
}
