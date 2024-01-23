<?php declare(strict_types=1);

namespace Core;

use Symfony\Component\HttpFoundation\Request;

abstract class Methods
{
    public const METHOD_GET    = Request::METHOD_GET;
    public const METHOD_POST   = Request::METHOD_POST;
    public const METHOD_PATCH  = Request::METHOD_PATCH;
    public const METHOD_DELETE = Request::METHOD_DELETE;
    public const METHOD_PUT    = Request::METHOD_PUT;

    public const AVAILABLE_METHODS = [
        self::METHOD_GET,
        self::METHOD_POST,
        self::METHOD_PATCH,
        self::METHOD_DELETE,
        self::METHOD_PUT,
    ];
}
