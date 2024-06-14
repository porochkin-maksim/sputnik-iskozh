<?php declare(strict_types=1);

namespace Core\Domains\News\Repositories;

use App\Models\News;
use Core\Db\RepositoryTrait;
use Core\Domains\News\Collections\NewsCollection;

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

    public function save(News $report): News
    {
        $report->save();

        return $report;
    }
}
