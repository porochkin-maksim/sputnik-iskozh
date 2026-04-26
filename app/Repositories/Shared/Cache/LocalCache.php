<?php declare(strict_types=1);

namespace App\Repositories\Shared\Cache;

class LocalCache
{
    private static bool  $cacheEnabled = true;
    private static array $storage      = [];
    private static array $expiresAt    = [];

    /**
     * TTL по умолчанию в секундах (null = бессрочно)
     */
    private static ?int $defaultTtl = 3600; // 1 час

    public static function setDefaultTtl(?int $ttl): void
    {
        self::$defaultTtl = $ttl;
    }

    public function has(string $cacheKey): bool
    {
        $this->cleanExpired($cacheKey);

        return array_key_exists($cacheKey, self::$storage);
    }

    public function get(string $cacheKey): mixed
    {
        if ( ! $this->isCacheEnabled()) {
            return null;
        }

        $this->cleanExpired($cacheKey);

        return self::$storage[$cacheKey] ?? null;
    }

    /**
     * @param string   $cacheKey
     * @param mixed    $object
     * @param int|null $ttl Время жизни в секундах. null = использовать defaultTtl
     */
    public function set(string $cacheKey, mixed $object, ?int $ttl = null): void
    {
        if ( ! $this->isCacheEnabled()) {
            return;
        }

        self::$storage[$cacheKey] = $object;

        $actualTtl                  = $ttl ?? self::$defaultTtl;
        self::$expiresAt[$cacheKey] = $actualTtl ? time() + $actualTtl : null;
    }

    public function drop(string ...$cacheKeys): void
    {
        foreach ($cacheKeys as $cacheKey) {
            unset(self::$storage[$cacheKey], self::$expiresAt[$cacheKey]);
        }
    }

    public function cleanExpired(?string $cacheKey = null): void
    {
        if ($cacheKey) {
            if (isset(self::$expiresAt[$cacheKey]) && self::$expiresAt[$cacheKey] < time()) {
                $this->drop($cacheKey);
            }

            return;
        }

        $now = time();
        foreach (self::$expiresAt as $key => $expire) {
            if ($expire && $expire < $now) {
                $this->drop($key);
            }
        }
    }

    public static function dropAll(): void
    {
        self::$storage   = [];
        self::$expiresAt = [];
    }

    private function isCacheEnabled(): bool
    {
        return self::$cacheEnabled;
    }

    public static function switchCacheEnabled(bool $cacheEnabled): void
    {
        self::$cacheEnabled = $cacheEnabled;
    }
}
