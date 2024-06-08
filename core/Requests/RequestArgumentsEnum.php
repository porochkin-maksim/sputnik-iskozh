<?php declare(strict_types=1);

namespace Core\Requests;

enum RequestArgumentsEnum: string
{
    public const ARTICLE      = 'article';
    public const CATEGORY     = 'category';
    public const EMAIL        = 'email';
    public const END_AT       = 'end_at';
    public const ID           = 'id';
    public const LIMIT        = 'limit';
    public const LOGIN        = 'login';
    public const MONEY        = 'money';
    public const NAME         = 'name';
    public const PASSWORD     = 'password';
    public const PUBLISHED_AT = 'published_at';
    public const REMEMBER     = 'remember';
    public const START_AT     = 'start_at';
    public const TITLE        = 'title';
    public const TYPE         = 'type';
    public const YEAR         = 'year';
}
