<?php declare(strict_types=1);

namespace Core\Db;

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
        bool              $cache = false,
    ): array
    {
        $result     = [];
        $modelClass = $this->modelClass();

        if (class_exists($this->modelClass())) {
            if ($cache && $this instanceof UseCacheRepositoryInterface) {
                /** @var Model[] $cachedModels */
                $result    = $this->cacheRepository()->getByKeys($ids);
                $cachedIds = array_map(fn($model) => $model->id, $result);
                $ids       = array_diff($ids, $cachedIds);
            }

            /** @var Model $model */
            $model = new $modelClass;
            $query = $model::select($searcher?->getSelect() ? : ['*'])->whereIn('id', $ids);

            if ($searcher) {
                $query = $query->when($searcher->getWith(), function (Builder $query) use ($searcher) {
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

            $result = array_merge($result, $query->get()->all());

            if ($cache && $this instanceof UseCacheRepositoryInterface) {
                foreach ($result as $item) {
                    /** @var Model $item */
                    if ( ! in_array($item->id, $cachedIds)) {
                        $this->cacheRepository()->add($item->id, $item);
                    }
                }
            }
        }

        return array_values($result);
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
