<?php declare(strict_types=1);

namespace Core\Requests;

enum RequestArgumentsEnum: string
{
    public const CATEGORY = 'category';
    public const EMAIL    = 'email';
    public const END_AT   = 'end_at';
    public const ID       = 'id';
    public const LOGIN    = 'login';
    public const MONEY    = 'money';
    public const NAME     = 'name';
    public const PASSWORD = 'password';
    public const REMEMBER = 'remember';
    public const START_AT = 'start_at';
    public const TYPE     = 'type';
    public const YEAR     = 'year';
}
