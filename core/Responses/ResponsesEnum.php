<?php declare(strict_types=1);

namespace Core\Responses;

enum ResponsesEnum: string
{
    public const ACCOUNT    = 'account';
    public const CATEGORIES = 'categories';
    public const COUNTER    = 'counter';
    public const COUNTERS   = 'counters';
    public const CREATE     = 'create';
    public const EDIT       = 'edit';
    public const VIEW       = 'view';
    public const DROP       = 'drop';
    public const FILES      = 'files';
    public const FOLDER     = 'folder';
    public const FOLDERS    = 'folders';
    public const NEWS       = 'news';
    public const OPTION     = 'option';
    public const OPTIONS    = 'options';
    public const PERIOD     = 'period';
    public const PERIODS    = 'periods';
    public const REPORT     = 'report';
    public const REPORTS    = 'reports';
    public const SERVICE    = 'service';
    public const SERVICES   = 'services';
    public const TOTAL      = 'total';
    public const TYPES      = 'types';
    public const MESSAGE    = 'message';
    public const ROLE       = 'role';
}
