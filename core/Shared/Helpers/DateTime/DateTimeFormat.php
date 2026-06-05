<?php declare(strict_types=1);

namespace Core\Shared\Helpers\DateTime;

use ReflectionClass;

abstract class DateTimeFormat
{
    public const string DATE_DEFAULT          = 'Y-m-d';        // ISO формат даты
    public const string DATE_TIME_DEFAULT     = 'Y-m-d H:i:s';  // ISO формат с временем
    public const string DATE_TIME_MAIN        = 'Y-m-d H:i';    // ISO формат с временем без секунд
    public const string DATE_TIME_FRONT       = 'Y-m-d\TH:i:s'; // ISO формат с временем для фронта
    public const string DATE_VIEW_FORMAT      = 'd.m.Y';        // Российский формат даты (полный год)
    public const string DATE_TIME_VIEW_FORMAT = 'd.m.Y H:i';    // Российский формат даты и времени (без секунд)
    public const string DATE_TIME_VIEW_FULL   = 'd.m.Y H:i:s';  // Российский формат даты и времени
    public const string TIME_FULL             = 'H:i:s';        // ISO формат времени
    public const string RUS_DATE_SHORT        = 'd.m.y';        // Российский формат даты (короткий год)
    public const string RUS_DATE_DASH_FULL    = 'd-m-Y';        // Российский формат даты с дефисами (полный год)
    public const string RUS_DATE_DASH_SHORT   = 'd-m-y';        // Российский формат даты с дефисами (короткий год)
    public const string US_DATE_FULL          = 'm/d/Y';        // Западный формат даты (полный год)
    public const string US_DATE_SHORT         = 'm/d/y';        // Западный формат даты (короткий год)
    public const string US_DATE_SLASH_FULL    = 'Y/m/d';        // Западный формат даты со слешами (полный год)
    public const string RUS_DATE_TIME         = 'd.m.Y H:i:s';  // Российский формат даты и времени
    public const string US_DATE_TIME          = 'm/d/Y H:i:s';  // Западный формат даты и времени
    public const string RUS_DATE_TIME_SHORT   = 'd.m.Y H:i';    // Российский формат даты и времени (без секунд)
    public const string US_DATE_TIME_SHORT    = 'm/d/Y H:i';    // Западный формат даты и времени (без секунд)
    public const string COMPACT_DATE_FULL     = 'Ymd';          // Компактный формат даты (полный год)
    public const string COMPACT_DATE_RUS      = 'dmY';          // Компактный российский формат даты
    public const string COMPACT_DATE_US       = 'mdy';          // Компактный западный формат даты
    public const string TEXT_MONTH_SHORT      = 'd M Y';        // Формат с коротким названием месяца
    public const string TEXT_MONTH_SHORT_US   = 'M d Y';        // Западный формат с коротким названием месяца
    public const string TEXT_MONTH_FULL       = 'd F Y';        // Формат с полным названием месяца
    public const string TEXT_MONTH_FULL_US    = 'F d Y';        // Западный формат с полным названием месяца

    /**
     * Возвращает массив всех констант форматов дат
     *
     * @return array<string, string>
     */
    public static function constants(): array
    {
        return (new ReflectionClass(self::class))->getConstants();
    }
}
