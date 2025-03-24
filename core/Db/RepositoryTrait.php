<?php declare(strict_types=1);

namespace Core\Db;

use Core\Cache\CacheLocator;
use Core\Db\Searcher\BaseSearcher;
use Core\Db\Searcher\Models\Order;
use Core\Db\Searcher\Models\SearchResponse;
use Core\Db\Searcher\Models\Where;
use Core\Db\Searcher\SearcherInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

trait RepositoryTrait
{
    abstract protected function modelClass(): string;

    public function getById(?int $id): ?Model
    {
        $result     = null;
        $modelClass = $this->modelClass();

        if ($id && class_exists($this->modelClass())) {
            $cacheKey = $this->cacheKey($id);

            if (CacheLocator::LocalCache()->has($cacheKey)) {
                $result = CacheLocator::LocalCache()->get($cacheKey);
            }
            else {
                /** @var Model $model */
                $model  = new $modelClass;
                $result = $model::find($id);

                if ($result) {
                    CacheLocator::LocalCache()->set($cacheKey, $result);
                }
            }
        }

        return $result;
    }

    private function cacheKey($key): string
    {
        return Hash::make($this->modelClass() . $key);
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
            $searcher = $searcher ?? new BaseSearcher();
            $searcher->setIds($ids);
            $query  = $this->buildSearchQuery($searcher);
            $query  = $this->buildCommonQuery($query, $searcher);

            // $query->when($searcher->getLimit() !== null, function (Builder $query) use ($searcher) {
            //     $query->limit($searcher->getLimit());
            // })->when($searcher->getOffset() !== null, function (Builder $query) use ($searcher) {
            //     $query->offset($searcher->getOffset());
            // })->when($searcher->getLastId() !== null, function (Builder $query) use ($searcher) {
            //     $query->where('id', SearcherInterface::GT, $searcher->getLastId());
            // })->when($searcher->getIds() !== null, function (Builder $query) use ($searcher) {
            //     $query->whereIn('id', $searcher->getIds());
            // });
            $result = array_merge($result, $query->get()->all());
        }

        return $result;
    }

    public function search(SearcherInterface $searcher): SearchResponse
    {
        $result = new SearchResponse();

        $cacheKey = md5(serialize($searcher));
        if (CacheLocator::LocalCache()->has($cacheKey)) {
            return CacheLocator::LocalCache()->get($cacheKey);
        }

        $query = $this->buildSearchQuery($searcher);

        $result->setTotal($query->count());

        $items = $this->buildCommonQuery($query, $searcher)->get();

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
            $query->select($searcher?->getSelect());
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
                        case SearcherInterface::GT:
                        case SearcherInterface::GTE:
                        case SearcherInterface::LT:
                        case SearcherInterface::LTE:
                        case SearcherInterface::IS_NOT:
                        case SearcherInterface::EQUALS:
                        case SearcherInterface::LIKE:
                            $query->where($where->getField(), $where->getOperator(), $where->getValue());
                            break;
                        case SearcherInterface::IS_NULL:
                            $query->whereNull($where->getField());
                            break;
                        case SearcherInterface::NOT_IN:
                            $query->whereNotIn($where->getField(), $where->getValue());
                            break;
                        case SearcherInterface::IN:
                            $query->whereIn($where->getField(), $where->getValue());
                            break;
                    }
                }
            })->when($searcher->getOrWhere(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getOrWhere() as $where) {
                    switch ($where->getOperator()) {
                        case SearcherInterface::GT:
                        case SearcherInterface::GTE:
                        case SearcherInterface::LT:
                        case SearcherInterface::LTE:
                        case SearcherInterface::IS_NOT:
                        case SearcherInterface::EQUALS:
                        case SearcherInterface::LIKE:
                            $query->orWhere($where->getField(), $where->getOperator(), $where->getValue());
                            break;
                        case SearcherInterface::IS_NULL:
                            $query->orWhereNull($where->getField());
                            break;
                        case SearcherInterface::NOT_IN:
                            $query->orWhereNotIn($where->getField(), $where->getValue());
                            break;
                        case SearcherInterface::IN:
                            $query->orWhereIn($where->getField(), $where->getValue());
                            break;
                    }
                }
            })->when($searcher->getWhereColumn(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getWhereColumn() as $where) {
                    $query->whereColumn($where->getField(), $where->getOperator(), $where->getValue());
                }
            })->when($searcher->getSortProperties(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getSortProperties() as $sort) {
                    $query->orderBy($sort->getField(), $sort->getValue());
                }
            });
        }

        return $query;
    }

    private function buildCommonQuery(Builder $query, SearcherInterface $searcher): Builder
    {
        $query->when($searcher->getLimit() !== null, function (Builder $query) use ($searcher) {
            $query->limit($searcher->getLimit());
        })->when($searcher->getOffset() !== null, function (Builder $query) use ($searcher) {
            $query->offset($searcher->getOffset());
        })->when($searcher->getLastId() !== null, function (Builder $query) use ($searcher) {
            $query->where('id', SearcherInterface::GT, $searcher->getLastId());
        })->when($searcher->getIds() !== null, function (Builder $query) use ($searcher) {
            $query->whereIn('id', $searcher->getIds());
        });

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
            $result = (new $modelClass)::whereIn('id', $ids)->delete();
        }

        foreach ($ids as $id) {
            $cacheKey = $this->cacheKey($id);
            CacheLocator::LocalCache()->drop($cacheKey);
        }

        return (bool) $result;
    }
}
