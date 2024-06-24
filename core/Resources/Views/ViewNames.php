<?php

namespace Core\Resources\Views;

enum ViewNames: string
{
    public const LAYOUTS_APP        = 'layouts.app';
    public const LAYOUTS_TWO_COLUMN = 'layouts.pages.two-columns-page';
    public const LAYOUTS_ONE_COLUMN = 'layouts.pages.single-column-page';

    public const METRICS = 'layouts.partial.metrics';

    public const PAGES_INDEX         = 'pages.index';
    public const PAGES_CONTACTS      = 'pages.contacts';
    public const PAGES_PRIVACY       = 'pages.privacy';
    public const PAGES_FILES_INDEX   = 'pages.files.index';
    public const PAGES_NEWS_INDEX    = 'pages.news.index';
    public const PAGES_NEWS_SHOW     = 'pages.news.show';
    public const PAGES_REPORTS_INDEX = 'pages.reports.index';

    public const PAGES_OPTIONS_INDEX = 'pages.options.index';

    public const HOME_LAYOUT            = 'pages.home.layouts.layout';
    public const PAGES_HOME             = 'pages.home.index';
    public const PAGES_ACCOUNT_REGISTER = 'pages.home.register';
    public const PAGES_PROFILE          = 'pages.home.profile';
    public const PAGES_COUNTERS_INDEX   = 'pages.counters.index';
}
