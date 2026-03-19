<?php declare(strict_types=1);

namespace Core\Db;

use Core\Cache\CacheLocator;
use Core\Db\Searcher\BaseSearcher;
use Core\Db\Searcher\Models\SearchResponse;
use Core\Db\Searcher\SearcherInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait RepositoryTrait
{
    abstract protected function modelClass(): string;

    /**
     * @deprecated
     */
    public function getById(?int $id): ?Model
    {
        return $this->getModelById($id);
    }

    /**
     * @param int[] $ids
     *
     * @deprecated
     */
    public function getByIds(
        array              $ids,
        ?SearcherInterface $searcher = null,
    ): array
    {
        $result = [];
        if (class_exists($this->modelClass())) {
            $searcher = $searcher ?? new BaseSearcher();
            $searcher->setIds($ids);
            $query  = $this->buildSearchQuery($searcher);
            $query  = $this->buildCommonQuery($query, $searcher);
            $result = array_merge($result, $query->get()->all());
        }

        return $result;
    }

    /**
     * @deprecated
     */
    public function search(SearcherInterface $searcher): SearchResponse
    {
        return $this->searchModels($searcher);
    }

    protected function getModelById(?int $id): ?Model
    {
        if ( ! $id) {
            return null;
        }
        $cacheKey = $this->cacheKey($id);
        if (CacheLocator::LocalCache()->has($cacheKey)) {
            return CacheLocator::LocalCache()->get($cacheKey);
        }
        $modelClass = $this->modelClass();
        $query      = (new $modelClass)::query();
        $query      = $this->getQuery($query);
        $model      = $query->find($id);
        if ($model) {
            CacheLocator::LocalCache()->set($cacheKey, $model);
        }

        return $model;
    }

    protected function searchModels(SearcherInterface $searcher): SearchResponse
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

        CacheLocator::LocalCache()->set($cacheKey, $result);

        return $result;
    }

    public function deleteById(?int $id): bool
    {
        return $this->deleteModelById($id);
    }

    protected function deleteModelById(?int $id): bool
    {
        return $id && $this->deleteModelsByIds([$id]) > 0;
    }

    protected function deleteModelsByIds(array $ids): int
    {
        $modelClass = $this->modelClass();
        $deleted    = (new $modelClass)::whereIn('id', $ids)->delete();
        $localCache = CacheLocator::LocalCache();
        foreach ($ids as $id) {
            $localCache->drop($this->cacheKey($id));
        }

        return $deleted;
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
        $query = $this->getQuery($query);

        if ($searcher) {
            $query->when($searcher->getWith(), function (Builder $query) use ($searcher) {
                $query->with($searcher->getWith());
            })->when($searcher->getWhere(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getWhere() as $where) {
                    $field = $this->adaptFieldName($where->getField());
                    $value = $where->getValue();

                    switch ($where->getOperator()) {
                        case SearcherInterface::GT:
                        case SearcherInterface::GTE:
                        case SearcherInterface::LT:
                        case SearcherInterface::LTE:
                        case SearcherInterface::IS_NOT:
                        case SearcherInterface::EQUALS:
                        case SearcherInterface::LIKE:
                            $query->where($field, $where->getOperator(), $value);
                            break;
                        case SearcherInterface::IS_NULL:
                            $query->whereNull($field);
                            break;
                        case SearcherInterface::IS_NOT_NULL:
                            $query->whereNotNull($field);
                            break;
                        case SearcherInterface::NOT_IN:
                            $query->whereNotIn($field, $value);
                            break;
                        case SearcherInterface::IN:
                            $query->whereIn($field, $value);
                            break;
                    }
                }
            })->when($searcher->getOrWhere(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getOrWhere() as $where) {
                    $field = $this->adaptFieldName($where->getField());
                    $value = $where->getValue();

                    switch ($where->getOperator()) {
                        case SearcherInterface::GT:
                        case SearcherInterface::GTE:
                        case SearcherInterface::LT:
                        case SearcherInterface::LTE:
                        case SearcherInterface::IS_NOT:
                        case SearcherInterface::EQUALS:
                        case SearcherInterface::LIKE:
                            $query->orWhere($field, $where->getOperator(), $value);
                            break;
                        case SearcherInterface::IS_NULL:
                            $query->orWhereNull($field);
                            break;
                        case SearcherInterface::NOT_IN:
                            $query->orWhereNotIn($field, $value);
                            break;
                        case SearcherInterface::IN:
                            $query->orWhereIn($field, $value);
                            break;
                    }
                }
            })->when($searcher->getWhereColumn(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getWhereColumn() as $where) {
                    $query->whereColumn($where->getField(), $where->getOperator(), $where->getValue());
                }
            })->when($searcher->getSortProperties(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getSortProperties() as $sort) {
                    $query->orderBy($this->adaptFieldName($sort->getField()), $sort->getValue());
                }
            })->when($searcher->getGroupsBy(), function (Builder $query) use ($searcher) {
                $query->groupBy(...$searcher->getGroupsBy());
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
            $query->where($this->adaptFieldName('id'), SearcherInterface::GT, $searcher->getLastId());
        })->when($searcher->getIds() !== null, function (Builder $query) use ($searcher) {
            $query->whereIn($this->adaptFieldName('id'), $searcher->getIds());
        })->when($searcher->getWithTrashed() !== null, function (Builder $query) use ($searcher) {
            $query->withTrashed();
        });

        return $query;
    }

    protected function getQuery(Builder $query): Builder
    {
        return $query;
    }

    protected function adaptFieldName(string $field): string
    {
        if (Str::contains($field, '.')) {
            return $field;
        }

        return sprintf("%s.%s", static::TABLE, $field);
    }

    private function cacheKey($key): string
    {
        return Hash::make($this->modelClass() . $key);
    }
}
