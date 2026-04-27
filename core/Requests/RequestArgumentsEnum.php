<?php declare(strict_types=1);

namespace Core\Requests;

enum RequestArgumentsEnum: string
{
    public const string LIMIT     = 'limit';
    public const string SKIP      = 'skip';
    public const string SORT_BY   = 'sort_by';
    public const string SORT_DESC = 'sort_desc';
    public const string SEARCH    = 'search';
}
