<?php declare(strict_types=1);

namespace Core\Resources;

abstract class RouteNames
{
    public const string INDEX       = 'index';
    public const string CONTACTS    = 'contacts';
    public const string GARBAGE     = 'garbage';
    public const string PRIVACY     = 'privacy';
    public const string LOGOUT      = 'logout';
    public const string HOME        = 'home';
    public const string REGULATION  = 'regulation';
    public const string SEARCH      = 'search';
    public const string SITE_SEARCH = 'search.site';

    public const string PASSWORD_SET  = 'password.set';
    public const string PASSWORD_SAVE = 'password.save';

    public const string REQUESTS                 = 'requests';
    public const string REQUESTS_PROPOSAL        = 'proposal';
    public const string REQUESTS_PROPOSAL_CREATE = 'proposal.create';
    public const string REQUESTS_PAYMENT         = 'payment';
    public const string REQUESTS_PAYMENT_CREATE  = 'payment.create';
    public const string REQUESTS_COUNTER         = 'counter';
    public const string REQUESTS_COUNTER_CREATE  = 'counter.create';

    public const string TEMPLATE_GET    = 'template.get';
    public const string TEMPLATE_UPDATE = 'template.update';

    public const string SESSION_STORE    = 'session.store';
    public const string COOKIE_AGREEMENT = 'cookie_agreement';

    public const string REPORTS             = 'reports.index';
    public const string REPORTS_LIST        = 'reports.list';
    public const string REPORTS_CREATE      = 'reports.create';
    public const string REPORTS_EDIT        = 'reports.edit';
    public const string REPORTS_SAVE        = 'reports.save';
    public const string REPORTS_DELETE      = 'reports.delete';
    public const string REPORTS_FILE_UPLOAD = 'reports.file.upload';
    public const string REPORTS_FILE_DELETE = 'reports.file.delete';

    public const string NEWS             = 'news.index';
    public const string NEWS_LIST        = 'news.list';
    public const string NEWS_LIST_ALL    = 'news.list.all';
    public const string NEWS_LIST_LOCKED = 'news.list.locked';
    public const string NEWS_CREATE      = 'news.create';
    public const string NEWS_SHOW        = 'news.show';
    public const string NEWS_EDIT        = 'news.edit';
    public const string NEWS_SAVE        = 'news.save';
    public const string NEWS_DELETE      = 'news.delete';
    public const string NEWS_FILE_SAVE   = 'news.file.save';
    public const string NEWS_FILE_UPLOAD = 'news.file.upload';
    public const string NEWS_FILE_DELETE = 'news.file.delete';

    public const string ANNOUNCEMENTS      = 'announcements.index';
    public const string ANNOUNCEMENTS_LIST = 'announcements.list';
    public const string ANNOUNCEMENTS_SHOW = 'announcements.show';

    public const string FILES         = 'files.index';
    public const string FILES_LIST    = 'files.list';
    public const string FILES_SAVE    = 'files.save';
    public const string FILES_REPLACE = 'files.replace';
    public const string FILES_EDIT    = 'files.edit';
    public const string FILES_DELETE  = 'files.delete';
    public const string FILES_STORE   = 'files.store';
    public const string FILES_UP      = 'files.up';
    public const string FILES_DOWN    = 'files.down';
    public const string FILES_MOVE    = 'files.move';

    public const string FOLDERS_LIST   = 'folders.list';
    public const string FOLDERS_SAVE   = 'folders.save';
    public const string FOLDERS_SHOW   = 'folders.show';
    public const string FOLDERS_DELETE = 'folders.delete';

    public const string ACCOUNT_REGISTER      = 'account.register';
    public const string ACCOUNT_REGISTER_SAVE = 'account.register.save';

    public const string PROFILE                = 'profile.show';
    public const string PROFILE_SAVE           = 'profile.save';
    public const string PROFILE_SAVE_EMAIL     = 'profile.save.email';
    public const string PROFILE_SAVE_PASSWORD  = 'profile.save.password';
    public const string PROFILE_SWITCH_ACCOUNT = 'profile.account.switch';

    public const string PROFILE_INDEX             = 'profile.index';
    public const string PROFILE_COUNTERS          = 'profile.counters.index';
    public const string PROFILE_COUNTER_VIEW      = 'profile.counters.view';
    public const string PROFILE_COUNTER_INCREMENT = 'profile.counters.increment-save';
    public const string PROFILE_INVOICES          = 'profile.invoices.index';

    public const string PROFILE_COUNTERS_LIST     = 'profile.counter.list';
    public const string PROFILE_COUNTER_CREATE    = 'profile.counter.create';
    public const string PROFILE_COUNTER_HISTORY   = 'profile.counter.history-list';
    public const string PROFILE_COUNTER_ADD_VALUE = 'profile.counter.add-value';

    public const string HISTORY_CHANGES = 'infra.history-changes';

    public const string SUMMARY           = 'common.summary';
    public const string SUMMARY_DETAILING = 'common.summary.detailing';

    public const string ADMIN = 'admin.index';

    public const string ADMIN_ROLE_INDEX  = 'admin.role.index';
    public const string ADMIN_ROLE_CREATE = 'admin.role.create';
    public const string ADMIN_ROLE_SAVE   = 'admin.role.save';
    public const string ADMIN_ROLE_LIST   = 'admin.role.list';
    public const string ADMIN_ROLE_DELETE = 'admin.role.delete';

    public const string ADMIN_USER_INDEX          = 'admin.user.index';
    public const string ADMIN_USER_VIEW           = 'admin.user.view';
    public const string ADMIN_USER_SAVE           = 'admin.user.save';
    public const string ADMIN_USER_GENERATE_EMAIL = 'admin.user.generate-email';
    public const string ADMIN_USER_LIST           = 'admin.user.list';
    public const string ADMIN_USER_DELETE         = 'admin.user.delete';
    public const string ADMIN_USER_RESTORE        = 'admin.user.restore';
    public const string ADMIN_USER_EXPORT         = 'admin.user.export';

    public const string ADMIN_OPTIONS_INDEX = 'admin.options.index';
    public const string ADMIN_OPTIONS_LIST  = 'admin.options.list';
    public const string ADMIN_OPTIONS_SAVE  = 'admin.options.save';

    public const string ADMIN_USER_SEND_RESTORE_PASSWORD     = 'admin.user.send.restore.password';
    public const string ADMIN_USER_SEND_INVITE_WITH_PASSWORD = 'admin.user.send.invite-with-password';

    public const string ADMIN_SERVICE_INDEX  = 'admin.service.index';
    public const string ADMIN_SERVICE_CREATE = 'admin.service.create';
    public const string ADMIN_SERVICE_SAVE   = 'admin.service.save';
    public const string ADMIN_SERVICE_LIST   = 'admin.service.list';
    public const string ADMIN_SERVICE_DELETE = 'admin.service.delete';

    public const string ADMIN_PERIOD_INDEX  = 'admin.period.index';
    public const string ADMIN_PERIOD_CREATE = 'admin.period.create';
    public const string ADMIN_PERIOD_SAVE   = 'admin.period.save';
    public const string ADMIN_PERIOD_LIST   = 'admin.period.list';
    public const string ADMIN_PERIOD_DELETE = 'admin.period.delete';

    public const string ADMIN_ACCOUNT_INDEX  = 'admin.account.index';
    public const string ADMIN_ACCOUNT_VIEW   = 'admin.account.view';
    public const string ADMIN_ACCOUNT_GET    = 'admin.account.get';
    public const string ADMIN_ACCOUNT_CREATE = 'admin.account.create';
    public const string ADMIN_ACCOUNT_SAVE   = 'admin.account.save';
    public const string ADMIN_ACCOUNT_LIST   = 'admin.account.list';
    public const string ADMIN_ACCOUNT_DELETE = 'admin.account.delete';

    public const string ADMIN_ACCOUNT_INVOICE_LIST = 'admin.account.invoice.list';

    public const string ADMIN_COUNTER_CREATE    = 'admin.counter.create';
    public const string ADMIN_COUNTER_VIEW      = 'admin.counter.view';
    public const string ADMIN_COUNTER_SAVE      = 'admin.counter.save';
    public const string ADMIN_COUNTER_LIST      = 'admin.counter.list';
    public const string ADMIN_COUNTER_DELETE    = 'admin.counter.delete';
    public const string ADMIN_COUNTER_ADD_VALUE = 'admin.counter.add-value';

    public const string ADMIN_INVOICE_INDEX  = 'admin.invoice.index';
    public const string ADMIN_INVOICE_CREATE = 'admin.invoice.create';
    public const string ADMIN_INVOICE_SAVE   = 'admin.invoice.save';
    public const string ADMIN_INVOICE_VIEW   = 'admin.invoice.view';
    public const string ADMIN_INVOICE_GET    = 'admin.invoice.get';
    public const string ADMIN_INVOICE_LIST   = 'admin.invoice.list';
    public const string ADMIN_INVOICE_EXPORT = 'admin.invoice.export';
    public const string ADMIN_INVOICE_DELETE = 'admin.invoice.delete';

    public const string ADMIN_INVOICE_GET_ACCOUNTS_COUNT_WITHOUT_REGULAR = 'admin.invoice.get-accounts-count-without-regular';
    public const string ADMIN_INVOICE_CREATE_REGULAR_INVOICES            = 'admin.invoice.create-regular-invoices';

    public const string ADMIN_CLAIM_VIEW   = 'admin.claim.view';
    public const string ADMIN_CLAIM_CREATE = 'admin.claim.create';
    public const string ADMIN_CLAIM_SAVE   = 'admin.claim.save';
    public const string ADMIN_CLAIM_LIST   = 'admin.claim.list';
    public const string ADMIN_CLAIM_DELETE = 'admin.claim.delete';

    public const string ADMIN_PAYMENT_VIEW        = 'admin.payment.view';
    public const string ADMIN_PAYMENT_CREATE      = 'admin.payment.create';
    public const string ADMIN_PAYMENT_AUTO_CREATE = 'admin.payment.auto-create';
    public const string ADMIN_PAYMENT_SAVE        = 'admin.payment.save';
    public const string ADMIN_PAYMENT_LIST        = 'admin.payment.list';
    public const string ADMIN_PAYMENT_DELETE      = 'admin.payment.delete';

    public const string ADMIN_NEW_PAYMENT_INDEX    = 'admin.new-payment.index';
    public const string ADMIN_NEW_PAYMENT_VIEW     = 'admin.new-payment.view';
    public const string ADMIN_NEW_PAYMENT_SAVE     = 'admin.new-payment.save';
    public const string ADMIN_NEW_PAYMENT_LIST     = 'admin.new-payment.list';
    public const string ADMIN_NEW_PAYMENT_INVOICES = 'admin.new-payment.get-invoices';
    public const string ADMIN_NEW_PAYMENT_DELETE   = 'admin.new-payment.delete';

    public const string ADMIN_COUNTER_HISTORY_LIST = 'admin.counter.history.list';

    public const string ADMIN_REQUEST_COUNTER_HISTORY_INDEX          = 'admin.requests.counter-history.index';
    public const string ADMIN_REQUEST_COUNTER_HISTORY_LINK           = 'admin.requests.counter-history.link';
    public const string ADMIN_REQUEST_COUNTER_HISTORY_LIST           = 'admin.requests.counter-history.list';
    public const string ADMIN_REQUEST_COUNTER_HISTORY_DELETE         = 'admin.requests.counter-history.delete';
    public const string ADMIN_REQUEST_COUNTER_HISTORY_CONFIRM        = 'admin.requests.counter-history.confirm';
    public const string ADMIN_REQUEST_COUNTER_HISTORY_CONFIRM_DELETE = 'admin.requests.counter-history.confirm-delete';
    public const string ADMIN_REQUEST_COUNTER_HISTORY_CREATE_CLAIM   = 'admin.requests.counter-history.create-claim';

    public const string ADMIN_TOP_PANEL_INDEX  = 'admin.top-panel.index';
    public const string ADMIN_TOP_PANEL_SEARCH = 'admin.top-panel.search';
    public const string ADMIN_SELECTS_ACCOUNTS = 'admin.selects.accounts';
    public const string ADMIN_SELECTS_COUNTERS = 'admin.selects.counters';
    public const string ADMIN_ERRORS           = 'admin.error-logs.index';

    public const string ADMIN_QUEUE        = 'admin.queue';
    public const string ADMIN_QUEUE_STATUS = 'admin.queue.status';
    public const string ADMIN_QUEUE_START  = 'admin.queue.start';
    public const string ADMIN_QUEUE_STOP   = 'admin.queue.stop';
    public const string ADMIN_QUEUE_CLEAR  = 'admin.queue.clear';

    public static function name(mixed $key, string $default = ''): string
    {
        return match ($key) {
            self::INDEX                               => 'Главная',
            self::CONTACTS                            => 'Контакты',
            self::GARBAGE                             => 'Вывоз мусора',
            self::PRIVACY                             => 'Политика обработки персональных данных',
            self::REGULATION                          => 'Устав',
            self::SEARCH                              => 'Поиск по сайту',

            self::REQUESTS                            => 'Обращения',
            self::REQUESTS_PROPOSAL                   => 'Предложения',
            self::REQUESTS_PAYMENT                    => 'Платежи',
            self::REQUESTS_COUNTER                    => 'Показания',

            self::HOME                                => 'Личный кабинет',
            self::PROFILE                             => 'Профиль',
            self::LOGOUT                              => 'Выйти',

            self::PROFILE_COUNTERS                    => 'Счётчики',
            self::PROFILE_INVOICES                    => 'Счета',

            self::FILES                               => 'Файлы',
            self::NEWS                                => 'Новости',
            self::ANNOUNCEMENTS                       => 'Объявления',
            self::REPORTS                             => 'Отчёты',

            // админка
            self::ADMIN                               => 'О системе',
            self::ADMIN_USER_INDEX                    => 'Пользователи',
            self::ADMIN_OPTIONS_INDEX                 => 'Опции',
            self::ADMIN_ROLE_INDEX                    => 'Роли',
            self::ADMIN_SERVICE_INDEX                 => 'Услуги',
            self::ADMIN_PERIOD_INDEX                  => 'Периоды',
            self::ADMIN_ACCOUNT_INDEX                 => 'Участки',
            self::ADMIN_INVOICE_INDEX                 => 'Счета',
            self::ADMIN_NEW_PAYMENT_INDEX             => 'Новые платежи',
            self::ADMIN_REQUEST_COUNTER_HISTORY_INDEX => 'Счётчики',
            self::ADMIN_ERRORS                        => 'Журнал ошибок',
            self::ADMIN_QUEUE                         => 'Очереди',

            default                                   => $default,
        };
    }
}
