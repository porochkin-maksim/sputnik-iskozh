<?php declare(strict_types=1);

namespace Core\Db;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

trait RepositoryTrait
{
    abstract protected function modelClass(): string;

    public function getById(?int $id, bool $cache = false): ?Model
    {
        $result     = null;
        $modelClass = $this->modelClass();
        $useCache   = $cache && $this instanceof UseCacheRepositoryInterface;

        if (class_exists($this->modelClass())) {

            if ($useCache) {
                /** @var ?Model $result */
                $result = $this->cacheRepository()->getByKey($id)
                ;
            }

            if (!$result) {
                /** @var Model $model */
                $model  = new $modelClass;
                $result = $model::find($id);

                if ($result && $useCache) {
                    $this->cacheRepository()->add($id, $result)
                    ;
                }
            }
        }

        return $result;
    }

    /**
     * @param int[] $ids
     */
    public function getByIds(array $ids, bool $cache = false): array
    {
        $result     = [];
        $modelClass = $this->modelClass();
        $useCache   = $cache && $this instanceof UseCacheRepositoryInterface;

        if (class_exists($this->modelClass())) {
            if ($useCache) {
                /** @var Model[] $cachedModels */
                $result    = $this->cacheRepository()->getByKeys($ids)
                ;
                $cachedIds = array_map(fn($model) => $model->id, $result);
                $ids       = array_diff($ids, $cachedIds);
            }

            /** @var Model $model */
            $model  = new $modelClass;
            $result = array_merge($result, $model::whereIn('id', $ids)->get()->all());

            if ($useCache) {
                foreach ($result as $item) {
                    /** @var Model $item */
                    if (!in_array($item->id, $cachedIds)) {
                        $this->cacheRepository()->add($item->id, $item)
                        ;
                    }
                }
            }
        }

        return array_values($result);
    }
}
