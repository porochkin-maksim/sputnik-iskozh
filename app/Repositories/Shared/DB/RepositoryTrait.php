<?php declare(strict_types=1);

namespace App\Repositories\Shared\DB;

use App\Repositories\Shared\Cache\CacheLocator;
use Core\Shared\Collections\Collection;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\BaseSearchResponse;
use Core\Repositories\SearcherInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReturnTypeWillChange;

trait RepositoryTrait
{
    abstract protected function modelClass(): string;

    abstract protected function getTable(): string;

    abstract protected function getMapper(): ?RepositoryDataMapperInterface;

    abstract protected function getEmptyCollection(): Collection;

    #[ReturnTypeWillChange]
    abstract protected function getEmptySearchResponse(): BaseSearchResponse;

    #[ReturnTypeWillChange]
    abstract protected function getEmptySearcher(): SearcherInterface;

    public function getModelById(?int $id, ?SearcherInterface $searcher = null): ?Model
    {
        if ( ! $id) {
            return null;
        }
        $cacheKey = $this->cacheKey($id);
        if (CacheLocator::LocalCache()->has($cacheKey)) {
            return CacheLocator::LocalCache()->get($cacheKey);
        }
        $searcher   = $searcher ? : $this->getEmptySearcher();

        $query = $this->buildSearchQuery($searcher);
        $query = $this->buildCommonQuery($query, $searcher);

        $model = $query->find($id);
        if ($model) {
            CacheLocator::LocalCache()->set($cacheKey, $model);
        }

        return $model;
    }

    #[ReturnTypeWillChange]
    protected function searchModels(SearcherInterface $searcher): BaseSearchResponse
    {
        $result = $this->getEmptySearchResponse();

        $cacheKey = md5(serialize($searcher));
        if (CacheLocator::LocalCache()->has($cacheKey)) {
            return CacheLocator::LocalCache()->get($cacheKey);
        }

        $query = $this->buildSearchQuery($searcher);

        $result->setTotal($query->count());

        $items = $this->buildCommonQuery($query, $searcher)->get();

        $result->setItems($this->getMapper()->makeEntityFromRepositoryDatas($items));

        CacheLocator::LocalCache()->set($cacheKey, $result);

        return $result;
    }

    public function deleteById(?int $id): bool
    {
        return $this->deleteModelById($id);
    }

    protected function deleteModelById(?int $id): bool
    {
        return ($id ? $this->getModelById($id) : null)?->delete();
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
            })->when($searcher->getWhereIn(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getWhereIn() as $where) {
                    $query->whereIn($where->getField(), $where->getValue());
                }
            })->when($searcher->getSortProperties()->count(), function (Builder $query) use ($searcher) {
                foreach ($searcher->getSortProperties()->toArray() as $sort) {
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

    /**
     * функция для переопределения в наследниках
     */
    protected function getQuery(Builder $query): Builder
    {
        return $query;
    }


    /**
     * функция для переопределения в наследниках
     */
    protected function adaptFieldName(string $field): string
    {
        if (Str::contains($field, '.')) {
            return $field;
        }

        return sprintf("%s.%s", $this->getTable(), $field);
    }

    private function cacheKey($key): string
    {
        return md5($this->modelClass() . $key);
    }
}
