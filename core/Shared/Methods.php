<?php declare(strict_types=1);

namespace Core\Shared;

abstract class Methods
{
    public const string METHOD_GET    = 'GET';
    public const string METHOD_POST   = 'POST';
    public const string METHOD_PATCH  = 'PATCH';
    public const string METHOD_DELETE = 'DELETE';
    public const string METHOD_PUT    = 'PUT';

    public const array AVAILABLE_METHODS = [
        self::METHOD_GET,
        self::METHOD_POST,
        self::METHOD_PATCH,
        self::METHOD_DELETE,
        self::METHOD_PUT,
    ];
}
