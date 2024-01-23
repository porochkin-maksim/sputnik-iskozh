<?php declare(strict_types=1);

namespace Core\Cache;

use Illuminate\Support\Facades\Cache;
use JsonSerializable;

abstract class AbstractCacheRepository implements CacheRepositoryInterface
{
    protected const KEY_SEPARATOR = '_';
    protected const MINUTES       = 5;
    protected const HOURS         = 0;

    abstract protected function prefix(): CachePrefixEnum;

    private function buildKey(string|int $key): string
    {
        return sprintf('%s%s%s', $this->prefix()->md5(), self::KEY_SEPARATOR, $key);
    }

    public function add(string|int $key, JsonSerializable $value): bool
    {
        return Cache::add(
            $this->buildKey($key),
            $value,
            now()
                ->addHours(static::HOURS)
                ->addMinutes(static::MINUTES),
        );
    }

    public function delete(string|int $key): bool
    {
        return Cache::delete($this->buildKey($key));
    }

    public function getByKey(string|int $key)
    {
        return Cache::get($this->buildKey($key));
    }

    public function getByKeys(array $keys): array
    {
        $keys = array_map(fn($key) => $this->buildKey($key), $keys);

        return array_filter(Cache::get($keys, []), fn($item) => $item !== null);
    }
}
