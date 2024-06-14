<?php declare(strict_types=1);

namespace Core\Responses;

enum ResponsesEnum: string
{
    public const ACCOUNT    = 'account';
    public const CATEGORIES = 'categories';
    public const EDIT       = 'edit';
    public const FILES      = 'files';
    public const NEWS       = 'news';
    public const REPORT     = 'report';
    public const REPORTS    = 'reports';
    public const TOTAL      = 'total';
    public const TYPES      = 'types';
}
