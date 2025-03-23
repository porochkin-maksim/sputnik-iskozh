<?php

namespace Core\Resources\Views;

enum ViewNames: string
{
    public const LAYOUTS_APP        = 'layouts.app-layout';
    public const LAYOUTS_ADMIN      = 'layouts.admin-layout';
    public const LAYOUTS_PROFILE    = 'layouts.profile-layout';
    public const LAYOUTS_TWO_COLUMN = 'layouts.pages.two-columns-page';
    public const LAYOUTS_ONE_COLUMN = 'layouts.pages.single-column-page';

    public const PARTIAL_SOCIAL  = 'layouts.partial.social';
    public const PARTIAL_METRICS = 'layouts.partial.metrics';
    public const PARTIAL_FOOTER  = 'layouts.partial.social';

    public const PAGES_INDEX         = 'pages.index';
    public const PAGES_CONTACTS      = 'pages.contacts';
    public const PAGES_PROPOSAL      = 'pages.contacts.proposal';
    public const PAGES_PAYMENT       = 'pages.contacts.payment';
    public const PAGES_COUNTER       = 'pages.contacts.counter';
    public const PAGES_GARBAGE       = 'pages.garbage';
    public const PAGES_PRIVACY       = 'pages.privacy';
    public const PAGES_SEARCH        = 'pages.search';
    public const PAGES_FILES_INDEX   = 'pages.files.index';
    public const PAGES_NEWS_INDEX    = 'pages.news.index';
    public const PAGES_NEWS_SHOW     = 'pages.news.show';
    public const PAGES_REPORTS_INDEX = 'pages.reports.index';

    public const PAGES_ANNOUNCEMENT_INDEX = 'pages.announcement.index';

    public const PAGES_OPTIONS_INDEX = 'pages.options.index';

    public const PAGES_HOME             = 'pages.home.index';
    public const PAGES_ACCOUNT_REGISTER = 'pages.home.register';
    public const PAGES_PROFILE          = 'pages.home.profile';
    public const PAGES_PROFILE_COUNTERS = 'pages.home.counters';
    public const PAGES_PROFILE_PAYMENTS = 'pages.home.payments';

    public const ADMIN_PAGES_INDEX           = 'admin.pages.index';
    public const ADMIN_PAGES_ROLES           = 'admin.pages.roles';
    public const ADMIN_PAGES_USERS           = 'admin.pages.users';
    public const ADMIN_PAGES_SERVICES        = 'admin.pages.services';
    public const ADMIN_PAGES_PERIODS         = 'admin.pages.periods';
    public const ADMIN_PAGES_ACCOUNTS        = 'admin.pages.accounts';
    public const ADMIN_PAGES_INVOICES        = 'admin.pages.invoices';
    public const ADMIN_PAGES_PAYMENTS        = 'admin.pages.payments';
    public const ADMIN_PAGES_COUNTER_HISTORY = 'admin.pages.counter-history';
}
