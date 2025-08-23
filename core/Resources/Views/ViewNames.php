<?php

namespace Core\Resources\Views;

enum ViewNames: string
{
    public const string LAYOUTS_APP        = 'layouts.app-layout';
    public const string LAYOUTS_ADMIN      = 'layouts.admin-layout';
    public const string LAYOUTS_PROFILE    = 'layouts.profile-layout';
    public const string LAYOUTS_TWO_COLUMN = 'layouts.pages.two-columns-page';
    public const string LAYOUTS_ONE_COLUMN = 'layouts.pages.single-column-page';

    public const string PARTIAL_SOCIAL  = 'layouts.partial.social';
    public const string PARTIAL_METRICS = 'layouts.partial.metrics';
    public const string PARTIAL_FOOTER  = 'layouts.partial.social';

    public const string PAGES_INDEX         = 'pages.index';
    public const string PAGES_CONTACTS      = 'pages.contacts';
    public const string PAGES_PROPOSAL      = 'pages.contacts.proposal';
    public const string PAGES_PAYMENT       = 'pages.contacts.payment';
    public const string PAGES_COUNTER       = 'pages.contacts.counter';
    public const string PAGES_GARBAGE       = 'pages.garbage';
    public const string PAGES_PRIVACY       = 'pages.privacy';
    public const string PAGES_SEARCH        = 'pages.search';
    public const string PAGES_FILES_INDEX   = 'pages.files.index';
    public const string PAGES_NEWS_INDEX    = 'pages.news.index';
    public const string PAGES_NEWS_SHOW     = 'pages.news.show';
    public const string PAGES_REPORTS_INDEX = 'pages.reports.index';

    public const string PAGES_ANNOUNCEMENT_INDEX = 'pages.announcement.index';

    public const string PAGES_HOME             = 'pages.home.index';
    public const string PAGES_ACCOUNT_REGISTER = 'pages.home.register';
    public const string PAGES_PROFILE          = 'pages.home.profile';

    public const string PAGES_PROFILE_COUNTERS      = 'pages.home.counters.index';
    public const string PAGES_PROFILE_COUNTERS_VIEW = 'pages.home.counters.view';

    public const string PAGES_PROFILE_INVOICES = 'pages.home.invoices';

    public const string ADMIN_PAGES_INDEX           = 'admin.pages.index';
    public const string ADMIN_PAGES_ROLES           = 'admin.pages.roles';
    public const string ADMIN_PAGES_USERS           = 'admin.pages.users';
    public const string ADMIN_PAGES_OPTIONS         = 'admin.pages.options';
    public const string ADMIN_PAGES_SERVICES        = 'admin.pages.services';
    public const string ADMIN_PAGES_PERIODS         = 'admin.pages.periods';
    public const string ADMIN_PAGES_ACCOUNTS        = 'admin.pages.accounts';
    public const string ADMIN_PAGES_INVOICES        = 'admin.pages.invoices';
    public const string ADMIN_PAGES_PAYMENTS        = 'admin.pages.payments';
    public const string ADMIN_PAGES_QUEUE           = 'admin.pages.queue';
    public const string ADMIN_PAGES_COUNTER_HISTORY = 'admin.pages.counter-history';
    public const string ADMIN_PAGES_COUNTER         = 'admin.pages.accounts.counters.view';
}
