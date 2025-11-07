<?php

namespace App\Http\Middleware\Enums;

enum MiddlewareNames: string
{
    public const string AUTH             = 'auth';
    public const string AUTH_BASIC       = 'auth.basic';
    public const string AUTH_SESSION     = 'auth.session';
    public const string CACHE_HEADERS    = 'cache.headers';
    public const string CAN              = 'can';
    public const string GUEST            = 'guest';
    public const string PASSWORD_CONFIRM = 'password.confirm';
    public const string PRECOGNITIVE     = 'precognitive';
    public const string SIGNED           = 'signed';
    public const string THROTTLE         = 'throttle';
    public const string VERIFIED         = 'verified';
    public const string ADMIN           = 'admin';
}
