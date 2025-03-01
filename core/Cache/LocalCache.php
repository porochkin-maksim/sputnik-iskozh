<?php declare(strict_types=1);

namespace Core\Cache;

class LocalCache
{
    private static bool  $cacheEnabled = true;
    private static array $storage      = [];


    public function has(string $cacheKey): bool
    {
        return array_key_exists($cacheKey, self::$storage);
    }

    /**
     * @return null|mixed
     */
    public function get(string $cacheKey): mixed
    {
        if ( ! $this->isCacheEnabled()) {
            return null;
        }

        return self::$storage[$cacheKey] ?? null;
    }

    public function set(string $cacheKey, mixed $object): void
    {
        if ( ! $this->isCacheEnabled()) {
            return;
        }

        self::$storage[$cacheKey] = $object;
    }

    public function drop(string ...$cacheKeys): void
    {
        foreach ($cacheKeys as $cacheKey) {
            unset(self::$storage[$cacheKey]);
        }
    }

    private function isCacheEnabled(): bool
    {
        return self::$cacheEnabled;
    }

    public static function setCacheEnabled(bool $cacheEnabled): void
    {
        self::$cacheEnabled = $cacheEnabled;
    }

    public static function dropAll(): void
    {
        self::$storage = [];
    }
}
