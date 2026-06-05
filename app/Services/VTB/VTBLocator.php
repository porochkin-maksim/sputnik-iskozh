<?php declare(strict_types=1);

namespace App\Services\VTB;

use App\Services\VTB\Factories\ResponseFactory;

abstract class VTBLocator
{
    private static Api             $api;
    private static ResponseFactory $responseFactory;

    public static function api(ApiConfig $config): Api
    {
        return new Api(
            $config,
            self::ResponseFactory(),
        );
    }

    public static function ResponseFactory(): ResponseFactory
    {
        if ( ! isset(self::$responseFactory)) {
            self::$responseFactory = new ResponseFactory();
        }

        return self::$responseFactory;
    }
}
