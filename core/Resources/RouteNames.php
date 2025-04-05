<?php declare(strict_types=1);

namespace Core\Resources;

abstract class RouteNames
{
    public const INDEX       = 'index';
    public const CONTACTS    = 'contacts';
    public const GARBAGE     = 'garbage';
    public const PRIVACY     = 'privacy';
    public const LOGOUT      = 'logout';
    public const HOME        = 'home';
    public const REGULATION  = 'regulation';
    public const SEARCH      = 'search';
    public const SITE_SEARCH = 'search.site';

    public const PASSWORD_SET  = 'password.set';
    public const PASSWORD_SAVE = 'password.save';

    public const REQUESTS        = 'requests';
    public const PROPOSAL        = 'proposal';
    public const PROPOSAL_CREATE = 'proposal.create';
    public const PAYMENT         = 'payment';
    public const PAYMENT_CREATE  = 'payment.create';
    public const COUNTER         = 'counter';
    public const COUNTER_CREATE  = 'counter.create';

    public const TEMPLATE_GET    = 'template.get';
    public const TEMPLATE_UPDATE = 'template.update';

    public const SESSION_STORE    = 'session.store';
    public const COOKIE_AGREEMENT = 'cookie_agreement';

    public const REPORTS             = 'reports.index';
    public const REPORTS_LIST        = 'reports.list';
    public const REPORTS_CREATE      = 'reports.create';
    public const REPORTS_EDIT        = 'reports.edit';
    public const REPORTS_SAVE        = 'reports.save';
    public const REPORTS_DELETE      = 'reports.delete';
    public const REPORTS_FILE_UPLOAD = 'reports.file.upload';
    public const REPORTS_FILE_DELETE = 'reports.file.delete';

    public const NEWS             = 'news.index';
    public const NEWS_LIST        = 'news.list';
    public const NEWS_LIST_ALL    = 'news.list.all';
    public const NEWS_LIST_LOCKED = 'news.list.locked';
    public const NEWS_CREATE      = 'news.create';
    public const NEWS_SHOW        = 'news.show';
    public const NEWS_EDIT        = 'news.edit';
    public const NEWS_SAVE        = 'news.save';
    public const NEWS_DELETE      = 'news.delete';
    public const NEWS_FILE_SAVE   = 'news.file.save';
    public const NEWS_FILE_UPLOAD = 'news.file.upload';
    public const NEWS_FILE_DELETE = 'news.file.delete';

    public const ANNOUNCEMENTS      = 'announcements.index';
    public const ANNOUNCEMENTS_LIST = 'announcements.list';
    public const ANNOUNCEMENTS_SHOW = 'announcements.show';

    public const FILES         = 'files.index';
    public const FILES_LIST    = 'files.list';
    public const FILES_SAVE    = 'files.save';
    public const FILES_REPLACE = 'files.replace';
    public const FILES_EDIT    = 'files.edit';
    public const FILES_DELETE  = 'files.delete';
    public const FILES_STORE   = 'files.store';
    public const FILES_UP      = 'files.up';
    public const FILES_DOWN    = 'files.down';
    public const FILES_MOVE    = 'files.move';

    public const FOLDERS_LIST   = 'folders.list';
    public const FOLDERS_SAVE   = 'folders.save';
    public const FOLDERS_SHOW   = 'folders.show';
    public const FOLDERS_DELETE = 'folders.delete';

    public const OPTIONS      = 'options.index';
    public const OPTIONS_LIST = 'options.list';
    public const OPTIONS_SAVE = 'options.save';
    public const OPTIONS_EDIT = 'options.edit';

    public const ACCOUNT_REGISTER      = 'account.register';
    public const ACCOUNT_REGISTER_SAVE = 'account.register.save';

    public const PROFILE               = 'profile.show';
    public const PROFILE_SAVE          = 'profile.save';
    public const PROFILE_SAVE_EMAIL    = 'profile.save.email';
    public const PROFILE_SAVE_PASSWORD = 'profile.save.password';


    public const PROFILE_INDEX        = 'profile.index';
    public const PROFILE_COUNTERS     = 'profile.counters.index';
    public const PROFILE_COUNTER_VIEW = 'profile.counters.view';
    public const PROFILE_PAYMENTS     = 'profile.payments.index';

    public const PROFILE_COUNTERS_LIST     = 'profile.counter.list';
    public const PROFILE_COUNTER_CREATE    = 'profile.counter.create';
    public const PROFILE_COUNTER_HISTORY   = 'profile.counter.history-list';
    public const PROFILE_COUNTER_ADD_VALUE = 'profile.counter.add-value';

    public const HISTORY_CHANGES = 'infra.history-changes';

    public const SUMMARY           = 'common.summary';
    public const SUMMARY_DETAILING = 'common.summary.detailing';

    public const ADMIN = 'admin.index';

    public const ADMIN_ROLE_INDEX  = 'admin.role.index';
    public const ADMIN_ROLE_CREATE = 'admin.role.create';
    public const ADMIN_ROLE_SAVE   = 'admin.role.save';
    public const ADMIN_ROLE_LIST   = 'admin.role.list';
    public const ADMIN_ROLE_DELETE = 'admin.role.delete';

    public const ADMIN_USER_INDEX  = 'admin.user.index';
    public const ADMIN_USER_VIEW   = 'admin.user.view';
    public const ADMIN_USER_CREATE = 'admin.user.create';
    public const ADMIN_USER_SEARCH = 'admin.user.search';
    public const ADMIN_USER_SAVE   = 'admin.user.save';
    public const ADMIN_USER_LIST   = 'admin.user.list';
    public const ADMIN_USER_DELETE = 'admin.user.delete';

    public const ADMIN_USER_SEND_RESTORE_PASSWORD     = 'admin.user.send.restore.password';
    public const ADMIN_USER_SEND_INVITE_WITH_PASSWORD = 'admin.user.send.invite-with-password';

    public const ADMIN_SERVICE_INDEX  = 'admin.service.index';
    public const ADMIN_SERVICE_CREATE = 'admin.service.create';
    public const ADMIN_SERVICE_SAVE   = 'admin.service.save';
    public const ADMIN_SERVICE_LIST   = 'admin.service.list';
    public const ADMIN_SERVICE_DELETE = 'admin.service.delete';

    public const ADMIN_PERIOD_INDEX  = 'admin.period.index';
    public const ADMIN_PERIOD_CREATE = 'admin.period.create';
    public const ADMIN_PERIOD_SAVE   = 'admin.period.save';
    public const ADMIN_PERIOD_LIST   = 'admin.period.list';
    public const ADMIN_PERIOD_DELETE = 'admin.period.delete';

    public const ADMIN_ACCOUNT_INDEX  = 'admin.account.index';
    public const ADMIN_ACCOUNT_VIEW   = 'admin.account.view';
    public const ADMIN_ACCOUNT_GET    = 'admin.account.get';
    public const ADMIN_ACCOUNT_CREATE = 'admin.account.create';
    public const ADMIN_ACCOUNT_SAVE   = 'admin.account.save';
    public const ADMIN_ACCOUNT_LIST   = 'admin.account.list';
    public const ADMIN_ACCOUNT_DELETE = 'admin.account.delete';

    public const ADMIN_COUNTER_CREATE    = 'admin.counter.create';
    public const ADMIN_COUNTER_SAVE      = 'admin.counter.save';
    public const ADMIN_COUNTER_LIST      = 'admin.counter.list';
    public const ADMIN_COUNTER_DELETE    = 'admin.counter.delete';
    public const ADMIN_COUNTER_ADD_VALUE = 'admin.counter.add-value';

    public const ADMIN_INVOICE_INDEX  = 'admin.invoice.index';
    public const ADMIN_INVOICE_CREATE = 'admin.invoice.create';
    public const ADMIN_INVOICE_SAVE   = 'admin.invoice.save';
    public const ADMIN_INVOICE_VIEW   = 'admin.invoice.view';
    public const ADMIN_INVOICE_GET    = 'admin.invoice.get';
    public const ADMIN_INVOICE_LIST   = 'admin.invoice.list';
    public const ADMIN_INVOICE_EXPORT = 'admin.invoice.export';
    public const ADMIN_INVOICE_DELETE = 'admin.invoice.delete';

    public const ADMIN_INVOICE_GET_ACCOUNTS_COUNT_WITHOUT_REGULAR = 'admin.invoice.get-accounts-count-without-regular';
    public const ADMIN_INVOICE_CREATE_REGULAR_INVOICES            = 'admin.invoice.create-regular-invoices';

    public const ADMIN_CLAIM_VIEW   = 'admin.claim.view';
    public const ADMIN_CLAIM_CREATE = 'admin.claim.create';
    public const ADMIN_CLAIM_SAVE   = 'admin.claim.save';
    public const ADMIN_CLAIM_LIST   = 'admin.claim.list';
    public const ADMIN_CLAIM_DELETE = 'admin.claim.delete';

    public const ADMIN_PAYMENT_VIEW   = 'admin.payment.view';
    public const ADMIN_PAYMENT_CREATE = 'admin.payment.create';
    public const ADMIN_PAYMENT_SAVE   = 'admin.payment.save';
    public const ADMIN_PAYMENT_LIST   = 'admin.payment.list';
    public const ADMIN_PAYMENT_DELETE = 'admin.payment.delete';

    public const ADMIN_NEW_PAYMENT_INDEX    = 'admin.new-payment.index';
    public const ADMIN_NEW_PAYMENT_VIEW     = 'admin.new-payment.view';
    public const ADMIN_NEW_PAYMENT_SAVE     = 'admin.new-payment.save';
    public const ADMIN_NEW_PAYMENT_LIST     = 'admin.new-payment.list';
    public const ADMIN_NEW_PAYMENT_INVOICES = 'admin.new-payment.get-invoices';
    public const ADMIN_NEW_PAYMENT_DELETE   = 'admin.new-payment.delete';

    public const ADMIN_COUNTER_HISTORY_INDEX              = 'admin.counter-history.index';
    public const ADMIN_COUNTER_HISTORY_LINK               = 'admin.counter-history.link';
    public const ADMIN_COUNTER_HISTORY_LIST               = 'admin.counter-history.list';
    public const ADMIN_COUNTER_HISTORY_DELETE             = 'admin.counter-history.delete';
    public const ADMIN_COUNTER_HISTORY_CONFIRM            = 'admin.counter-history.confirm';
    public const ADMIN_COUNTER_HISTORY_CONFIRM_DELETE     = 'admin.counter-history.confirm-delete';
    public const ADMIN_COUNTER_HISTORY_CREATE_CLAIM       = 'admin.counter-history.create-claim';

    public const ADMIN_SELECTS_ACCOUNTS = 'admin.selects.accounts';
    public const ADMIN_SELECTS_COUNTERS = 'admin.selects.counters';

    public static function name(mixed $key, string $default = ''): string
    {
        return match ($key) {
            self::CONTACTS                    => 'Контакты',
            self::GARBAGE                     => 'Вывоз мусора',
            self::PRIVACY                     => 'Политика обработки персональных данных',
            self::REGULATION                  => 'Устав',
            self::SEARCH                      => 'Поиск по сайту',

            self::REQUESTS                    => 'Обращения',
            self::PROPOSAL                    => 'Предложения',
            self::PAYMENT                     => 'Платежи',
            self::COUNTER                     => 'Показания',

            self::HOME                        => 'Личный кабинет',
            self::PROFILE                     => 'Профиль',
            self::LOGOUT                      => 'Выйти',

            self::PROFILE_COUNTERS            => 'Счётчики',
            self::PROFILE_PAYMENTS            => 'Платежи',

            self::OPTIONS                     => 'Настройки',

            self::FILES                       => 'Файлы',
            self::NEWS                        => 'Новости',
            self::ANNOUNCEMENTS               => 'Объявления',
            self::REPORTS                     => 'Отчёты',

            // админка
            self::ADMIN                       => 'О системе',
            self::ADMIN_USER_INDEX            => 'Пользователи',
            self::ADMIN_ROLE_INDEX            => 'Роли',
            self::ADMIN_SERVICE_INDEX         => 'Услуги',
            self::ADMIN_PERIOD_INDEX          => 'Периоды',
            self::ADMIN_ACCOUNT_INDEX         => 'Участки',
            self::ADMIN_INVOICE_INDEX         => 'Счета',
            self::ADMIN_NEW_PAYMENT_INDEX     => 'Новые платежи',
            self::ADMIN_COUNTER_HISTORY_INDEX => 'Новые показания',

            default                           => $default,
        };
    }
}
