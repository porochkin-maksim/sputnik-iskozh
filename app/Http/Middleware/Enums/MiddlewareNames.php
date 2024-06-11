<?php

namespace App\Http\Middleware\Enums;

enum MiddlewareNames: string
{
    public const AUTH             = 'auth';
    public const AUTH_BASIC       = 'auth.basic';
    public const AUTH_SESSION     = 'auth.session';
    public const CACHE_HEADERS    = 'cache.headers';
    public const CAN              = 'can';
    public const GUEST            = 'guest';
    public const PASSWORD_CONFIRM = 'password.confirm';
    public const PRECOGNITIVE     = 'precognitive';
    public const SIGNED           = 'signed';
    public const THROTTLE         = 'throttle';
    public const VERIFIED         = 'verified';
}
