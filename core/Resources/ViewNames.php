<?php

namespace Core\Resources;

enum ViewNames: string
{
    public const LAYOUTS_APP         = 'layouts.app';
    public const PAGES_INDEX         = 'pages.index';
    public const PAGES_FILES_INDEX   = 'pages.files.index';
    public const PAGES_NEWS_INDEX    = 'pages.news.index';
    public const PAGES_NEWS_SHOW     = 'pages.news.show';
    public const PAGES_REPORTS_INDEX = 'pages.reports.index';

    public const PAGES_HOME = 'pages.home.index';
}
