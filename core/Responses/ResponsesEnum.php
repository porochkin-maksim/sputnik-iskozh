<?php declare(strict_types=1);

namespace Core\Responses;

enum ResponsesEnum: string
{
    public const ANSWER     = 'answer';
    public const ANSWERS    = 'answers';
    public const ACCOUNT    = 'account';
    public const CATEGORIES = 'categories';
    public const COUNTER    = 'counter';
    public const COUNTERS   = 'counters';
    public const EDIT       = 'edit';
    public const FILES      = 'files';
    public const FOLDER     = 'folder';
    public const FOLDERS    = 'folders';
    public const NEWS       = 'news';
    public const OPTION     = 'option';
    public const OPTIONS    = 'options';
    public const POLL       = 'poll';
    public const POLLS      = 'polls';
    public const QUESTION   = 'question';
    public const QUESTIONS  = 'questions';
    public const REPORT     = 'report';
    public const REPORTS    = 'reports';
    public const TOTAL      = 'total';
    public const TYPES      = 'types';
}
