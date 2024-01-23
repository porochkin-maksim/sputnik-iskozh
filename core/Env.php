<?php declare(strict_types=1);

namespace Core;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class Env
{
    private static array|null $server = null;

    public static function init(): void
    {
        self::$server = $_SERVER;
    }

    public static function appName(): string
    {
        return config('app.name');
    }

    public static function host(): string
    {
        return Str::remove(
            sprintf(':%s', self::appPort()),
            Str::remove(
                sprintf(':%s', self::port()),
                self::fullHost(),
            ),
        );
    }

    public static function fullHost(): string
    {
        return self::$server['HTTP_HOST'];
    }

    public static function appPort(): string
    {
        return self::$server['APP_PORT'];
    }

    public static function port(): string
    {
        return self::$server['SERVER_PORT'];
    }

    public function isProduction(): bool
    {
        return App::isProduction();
    }

    public function devUrl(): ?string
    {
        return config('app.dev-url');
    }

    public function baseDomain(): ?string
    {
        return config('app.server-name');
    }

    public function hostname(): ?string
    {
        return config('app.server-name');
    }
}
