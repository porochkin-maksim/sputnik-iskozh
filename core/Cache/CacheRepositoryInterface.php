<?php declare(strict_types=1);

namespace Core\Cache;

use JsonSerializable;

interface CacheRepositoryInterface
{
    public function add(string|int $key, JsonSerializable $value): bool;

    public function delete(string|int $key): bool;

    public function getByKey(string|int $key);

    /**
     * /**
     * @param string[]|int[] $keys
     */
    public function getByKeys(array $keys): array;
}
