<?php declare(strict_types=1);

namespace Core\Resources;

abstract class RouteNames
{
    public const INDEX    = 'index';
    public const CONTACTS = 'contacts';
    public const LOGOUT   = 'logout';
    public const HOME     = 'home';

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
    public const NEWS_CREATE      = 'news.create';
    public const NEWS_SHOW        = 'news.show';
    public const NEWS_EDIT        = 'news.edit';
    public const NEWS_SAVE        = 'news.save';
    public const NEWS_DELETE      = 'news.delete';
    public const NEWS_FILE_SAVE   = 'news.file.save';
    public const NEWS_FILE_UPLOAD = 'news.file.upload';
    public const NEWS_FILE_DELETE = 'news.file.delete';

    public const FILES        = 'files.index';
    public const FILES_LIST   = 'files.list';
    public const FILES_SAVE   = 'files.save';
    public const FILES_EDIT   = 'files.edit';
    public const FILES_DELETE = 'files.delete';
    public const FILES_STORE  = 'files.store';
    public const FILES_UP     = 'files.up';
    public const FILES_DOWN   = 'files.down';

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
            self::CONTACTS => 'Контакты',

            self::HOME     => 'Личный кабинет',
            self::PROFILE  => 'Профиль',
            self::LOGOUT   => 'Выйти',

            self::OPTIONS  => 'Настройки',

            self::FILES    => 'Файлы',
            self::NEWS     => 'Новости',
            self::REPORTS  => 'Отчёты',
            default        => $default,
        };
    }
}
