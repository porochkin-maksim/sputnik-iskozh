<?php

namespace Core\Resources\Views;

enum ViewNames: string
{
    public const LAYOUTS_APP        = 'layouts.app';
    public const LAYOUTS_TWO_COLUMN = 'layouts.pages.two-columns-page';

    public const PAGES_INDEX         = 'pages.index';
    public const PAGES_FILES_INDEX   = 'pages.files.index';
    public const PAGES_NEWS_INDEX    = 'pages.news.index';
    public const PAGES_NEWS_SHOW     = 'pages.news.show';
    public const PAGES_REPORTS_INDEX = 'pages.reports.index';

    public const PAGES_HOME             = 'pages.home.index';
    public const PAGES_ACCOUNT_REGISTER = 'pages.home.register';
    public const PAGES_PROFILE          = 'pages.home.profile';
}
