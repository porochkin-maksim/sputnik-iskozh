<?php declare(strict_types=1);

namespace Core\Requests;

enum RequestArgumentsEnum: string
{
    public const ARTICLE         = 'article';
    public const ALL             = 'all';
    public const CATEGORY        = 'category';
    public const DATA            = 'data';
    public const DESCRIPTION     = 'description';
    public const EMAIL           = 'email';
    public const END_AT          = 'end_at';
    public const FIRST_NAME      = 'first_name';
    public const FOLDER          = 'folder';
    public const FILE            = 'file';
    public const ID              = 'id';
    public const INDEX           = 'index';
    public const IS_LOCK         = 'is_lock';
    public const IS_MEMBER       = 'is_member';
    public const LAST_NAME       = 'last_name';
    public const LIMIT           = 'limit';
    public const LOGIN           = 'login';
    public const MIDDLE_NAME     = 'middle_name';
    public const MONEY           = 'money';
    public const NUMBER          = 'number';
    public const NAME            = 'name';
    public const PASSWORD        = 'password';
    public const PARENT_ID       = 'parent_id';
    public const PRIMARY_USER_ID = 'primary_user_id';
    public const PUBLISHED_AT    = 'published_at';
    public const REMEMBER        = 'remember';
    public const SEARCH          = 'search';
    public const SKIP            = 'skip';
    public const SORT_BY         = 'sort_by';
    public const SORT_DESC       = 'sort_desc';
    public const START_AT        = 'start_at';
    public const TITLE           = 'title';
    public const TYPE            = 'type';
    public const UID             = 'uid';
    public const YEAR            = 'year';
}
