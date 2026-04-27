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

    public const string PAGES_INDEX         = 'public.index';
    public const string PAGES_CONTACTS      = 'public.contacts';
    public const string PAGES_REQUESTS      = 'public.contacts.requests';
    public const string PAGES_PROPOSAL      = 'public.contacts.proposal';
    public const string PAGES_PAYMENT       = 'public.contacts.payment';
    public const string PAGES_COUNTER       = 'public.contacts.counter';
    public const string PAGES_GARBAGE       = 'public.garbage';
    public const string PAGES_PRIVACY       = 'public.privacy';
    public const string PAGES_SEARCH        = 'public.search';
    public const string PAGES_FILES_INDEX   = 'public.files.index';
    public const string PAGES_NEWS_INDEX    = 'public.news.index';
    public const string PAGES_NEWS_SHOW     = 'public.news.show';

    public const string PARTIAL_REQUESTS = 'public.partial.requests';

    public const string PAGES_ANNOUNCEMENT_INDEX = 'public.announcement.index';

    public const string PAGES_ACCOUNT_REGISTER = 'home.public.register';

    public const string PAGES_PROFILE_COUNTERS      = 'home.public.counters.index';
    public const string PAGES_PROFILE_COUNTERS_VIEW = 'home.public.counters.view';

    public const string PAGES_PROFILE_INVOICES = 'home.pages.invoices';

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
