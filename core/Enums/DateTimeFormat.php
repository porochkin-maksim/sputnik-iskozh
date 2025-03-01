<?php declare(strict_types=1);

namespace Core\Enums;

enum DateTimeFormat: string
{
    public const DATE_DEFAULT          = 'Y-m-d';
    public const DATE_TIME_DEFAULT     = 'Y-m-d H:i:s';
    public const DATE_TIME_FRONT       = 'Y-m-d\TH:i:s';
    public const DATE_VIEW_FORMAT      = 'd.m.Y';
    public const DATE_TIME_VIEW_FORMAT = 'd.m.Y H:i';
    public const DATE_TIME_VIEW_FULL   = 'd.m.Y H:i:s';
    public const TIME_FULL             = 'H:i:s';
}
