<?php declare(strict_types=1);

namespace Core\Responses;

enum ResponsesEnum: string
{
    public const CATEGORIES = 'categories';
    public const EDIT       = 'edit';
    public const REPORT     = 'report';
    public const REPORTS    = 'reports';
    public const TYPES      = 'types';
}
