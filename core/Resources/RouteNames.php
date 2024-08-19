<?php declare(strict_types=1);

namespace Core\Resources;

abstract class RouteNames
{
    public const INDEX      = 'index';
    public const CONTACTS   = 'contacts';
    public const GARBAGE    = 'garbage';
    public const PRIVACY    = 'privacy';
    public const LOGOUT     = 'logout';
    public const HOME       = 'home';
    public const REGULATION = 'regulation';
    public const RUBRICS    = 'rubrics';

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

    public static function name(mixed $key, string $default = ''): string
    {
        return match ($key) {
            self::CONTACTS      => 'Контакты',
            self::GARBAGE       => 'Вывоз мусора',
            self::PRIVACY       => 'Политика обработки персональных данных',
            self::REGULATION    => 'Устав',
            self::RUBRICS       => 'Рубрикатор',

            self::HOME          => 'Личный кабинет',
            self::PROFILE       => 'Профиль',
            self::LOGOUT        => 'Выйти',

            self::OPTIONS       => 'Настройки',

            self::FILES         => 'Файлы',
            self::NEWS          => 'Новости',
            self::ANNOUNCEMENTS => 'Объявления',
            self::REPORTS       => 'Отчёты',
            default             => $default,
        };
    }
}
