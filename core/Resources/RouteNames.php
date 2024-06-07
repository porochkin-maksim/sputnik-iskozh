<?php

namespace Core\Resources;

enum RouteNames: string
{
    public const INDEX = 'index';

    public const REPORTS             = 'reports.index';
    public const REPORTS_LIST        = 'reports.list';
    public const REPORTS_CREATE      = 'reports.create';
    public const REPORTS_EDIT        = 'reports.edit';
    public const REPORTS_SAVE        = 'reports.save';
    public const REPORTS_DELETE      = 'reports.delete';
    public const REPORTS_FILE_UPLOAD = 'reports.file.upload';
    public const REPORTS_FILE_DELETE = 'reports.file.delete';

    public const FILES        = 'files.index';
    public const FILES_LIST   = 'files.list';
    public const FILES_SAVE   = 'files.save';
    public const FILES_EDIT   = 'files.edit';
    public const FILES_DELETE = 'files.delete';
    public const FILES_STORE  = 'files.store';
}
