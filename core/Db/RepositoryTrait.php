<?php declare(strict_types=1);

namespace Core\Db;

use Core\Db\Searcher\Models\SearchResponse;
use Core\Db\Searcher\SearcherInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait RepositoryTrait
{
    abstract protected function modelClass(): string;

    public function getById(?int $id, bool $cache = false): ?Model
    {
        $result     = null;
        $modelClass = $this->modelClass();

        if (class_exists($this->modelClass())) {

            if ($cache && $this instanceof UseCacheRepositoryInterface) {
                /** @var ?Model $result */
                $result = $this->cacheRepository()->getByKey($id);
            }

            if ( ! $result) {
                /** @var Model $model */
                $model  = new $modelClass;
                $result = $model::find($id);

                if ($result && $cache && $this instanceof UseCacheRepositoryInterface) {
                    $this->cacheRepository()->add($id, $result);
                }
            }
        }

        return $result;
    }

    /**
     * @param int[] $ids
     */
    public function getByIds(
        array             $ids,
        SearcherInterface $searcher = null,
    ): array
    {
        $result = [];
        if (class_exists($this->modelClass())) {
            $query  = $this->buildSearchQuery($searcher)->whereIn('id', $ids);
            $result = array_merge($result, $query->get()->all());
        }

        return array_values($result);
    }

    public function search(SearcherInterface $searcher): SearchResponse
    {
        $result = new SearchResponse();
        $query  = $this->buildSearchQuery($searcher);

        $result->setTotal($query->count());

        $items = $query->when($searcher->getLimit() !== null, function (Builder $query) use ($searcher) {
            $query->limit($searcher->getLimit());
        })->when($searcher->getOffset() !== null, function (Builder $query) use ($searcher) {
            $query->offset($searcher->getOffset());
        })->when($searcher->getLastId() !== null, function (Builder $query) use ($searcher) {
            $query->where('id', SearcherInterface::GT, $searcher->getLastId());
        })->get();

        $result->setItems($items);

        return $result;
    }

    protected function buildSearchQuery(?SearcherInterface $searcher = null): Builder
    {
        $modelClass = $this->modelClass();
        /** @var Model $model */
        $model = new $modelClass;

        $query = $model::query();

        if ($searcher?->getSelect()) {
            $query->select($searcher->getSelect());
        }
        else {
            $query->select(['*']);
        }

        if ($searcher) {
            $query->when($searcher->getWith(), function (Builder $query) use ($searcher) {
                $query->with($searcher->getWith());
            })->when($searcher->getWhere(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getWhere() as $where) {
                    switch ($where->getOperator()) {
                        case SearcherInterface::EQUALS:
                            $query->where($where->getField(), $where->getOperator(), $where->getValue());
                            break;
                        case SearcherInterface::IS_NULL:
                            $query->whereNull($where->getField());
                    }

                }
            })->orderBy($searcher->getSortProperty(), $searcher->getSortOrder());
        }

        return $query;
    }

    public function deleteById(int $id): bool
    {
        return $this->deleteByIds([$id]);
    }

    /**
     * @param int[] $ids
     */
    public function deleteByIds(array $ids): bool
    {
        $result     = false;
        $modelClass = $this->modelClass();

        if (class_exists($this->modelClass())) {
            $model  = new $modelClass;
            $result = $model::whereIn('id', $ids)->delete();
        }

        return (bool) $result;
    }
}
