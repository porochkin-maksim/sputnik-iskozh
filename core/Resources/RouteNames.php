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
    public const RUBRICS     = 'rubrics';
    public const SEARCH      = 'search';
    public const SITE_SEARCH = 'search.site';

    public const PROPOSAL        = 'proposal';
    public const PROPOSAL_CREATE = 'proposal.create';

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
    public const ACCOUNT_INFO          = 'account.info';

    public const PROFILE               = 'profile.show';
    public const PROFILE_SAVE          = 'profile.save';
    public const PROFILE_SAVE_EMAIL    = 'profile.save.email';
    public const PROFILE_SAVE_PASSWORD = 'profile.save.password';

    public const PROFILE_COUNTERS_LIST = 'profile.counter.list';
    public const PROFILE_COUNTER_SAVE  = 'profile.counter.save';

    public const HISTORY_CHANGES = 'infra.history-changes';

    public const ADMIN = 'admin.index';

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
    public const ADMIN_ACCOUNT_CREATE = 'admin.account.create';
    public const ADMIN_ACCOUNT_SAVE   = 'admin.account.save';
    public const ADMIN_ACCOUNT_LIST   = 'admin.account.list';
    public const ADMIN_ACCOUNT_DELETE = 'admin.account.delete';

    public const ADMIN_INVOICE_INDEX  = 'admin.invoice.index';
    public const ADMIN_INVOICE_CREATE = 'admin.invoice.create';
    public const ADMIN_INVOICE_SAVE   = 'admin.invoice.save';
    public const ADMIN_INVOICE_VIEW   = 'admin.invoice.view';
    public const ADMIN_INVOICE_GET    = 'admin.invoice.get';
    public const ADMIN_INVOICE_LIST   = 'admin.invoice.list';
    public const ADMIN_INVOICE_DELETE = 'admin.invoice.delete';

    public const ADMIN_TRANSACTION_VIEW   = 'admin.transaction.view';
    public const ADMIN_TRANSACTION_CREATE = 'admin.transaction.create';
    public const ADMIN_TRANSACTION_SAVE   = 'admin.transaction.save';
    public const ADMIN_TRANSACTION_LIST   = 'admin.transaction.list';
    public const ADMIN_TRANSACTION_DELETE = 'admin.transaction.delete';

    public const ADMIN_PAYMENT_VIEW   = 'admin.payment.view';
    public const ADMIN_PAYMENT_CREATE = 'admin.payment.create';
    public const ADMIN_PAYMENT_SAVE   = 'admin.payment.save';
    public const ADMIN_PAYMENT_LIST   = 'admin.payment.list';
    public const ADMIN_PAYMENT_DELETE = 'admin.payment.delete';

    public static function name(mixed $key, string $default = ''): string
    {
        return match ($key) {
            self::CONTACTS            => 'Контакты',
            self::GARBAGE             => 'Вывоз мусора',
            self::PRIVACY             => 'Политика обработки персональных данных',
            self::REGULATION          => 'Устав',
            self::RUBRICS             => 'Рубрикатор',
            self::PROPOSAL            => 'Предложения',
            self::SEARCH              => 'Поиск по сайту',

            self::HOME                => 'Личный кабинет',
            self::PROFILE             => 'Профиль',
            self::LOGOUT              => 'Выйти',

            self::OPTIONS             => 'Настройки',

            self::FILES               => 'Файлы',
            self::NEWS                => 'Новости',
            self::ANNOUNCEMENTS       => 'Объявления',
            self::REPORTS             => 'Отчёты',

            // админка
            self::ADMIN               => 'О системе',
            self::ADMIN_SERVICE_INDEX => 'Услуги',
            self::ADMIN_PERIOD_INDEX  => 'Периоды',
            self::ADMIN_ACCOUNT_INDEX => 'Участки',
            self::ADMIN_INVOICE_INDEX => 'Счета',

            default                   => $default,
        };
    }
}
