<?php declare(strict_types=1);

namespace Core\Enums;

use ReflectionClass;

enum DateTimeFormat: string
{
    public const DATE_DEFAULT          = 'Y-m-d';        // ISO формат даты
    public const DATE_TIME_DEFAULT     = 'Y-m-d H:i:s';  // ISO формат с временем
    public const DATE_TIME_FRONT       = 'Y-m-d\TH:i:s'; // ISO формат с временем для фронта
    public const DATE_VIEW_FORMAT      = 'd.m.Y';        // Российский формат даты (полный год)
    public const DATE_TIME_VIEW_FORMAT = 'd.m.Y H:i';    // Российский формат даты и времени (без секунд)
    public const DATE_TIME_VIEW_FULL   = 'd.m.Y H:i:s';  // Российский формат даты и времени
    public const TIME_FULL             = 'H:i:s';        // ISO формат времени
    public const RUS_DATE_SHORT        = 'd.m.y';        // Российский формат даты (короткий год)
    public const RUS_DATE_DASH_FULL    = 'd-m-Y';        // Российский формат даты с дефисами (полный год)
    public const RUS_DATE_DASH_SHORT   = 'd-m-y';        // Российский формат даты с дефисами (короткий год)
    public const US_DATE_FULL          = 'm/d/Y';        // Западный формат даты (полный год)
    public const US_DATE_SHORT         = 'm/d/y';        // Западный формат даты (короткий год)
    public const US_DATE_SLASH_FULL    = 'Y/m/d';        // Западный формат даты со слешами (полный год)
    public const RUS_DATE_TIME         = 'd.m.Y H:i:s';  // Российский формат даты и времени
    public const US_DATE_TIME          = 'm/d/Y H:i:s';  // Западный формат даты и времени
    public const RUS_DATE_TIME_SHORT   = 'd.m.Y H:i';    // Российский формат даты и времени (без секунд)
    public const US_DATE_TIME_SHORT    = 'm/d/Y H:i';    // Западный формат даты и времени (без секунд)
    public const COMPACT_DATE_FULL     = 'Ymd';          // Компактный формат даты (полный год)
    public const COMPACT_DATE_RUS      = 'dmY';          // Компактный российский формат даты
    public const COMPACT_DATE_US       = 'mdy';          // Компактный западный формат даты
    public const TEXT_MONTH_SHORT      = 'd M Y';        // Формат с коротким названием месяца
    public const TEXT_MONTH_SHORT_US   = 'M d Y';        // Западный формат с коротким названием месяца
    public const TEXT_MONTH_FULL       = 'd F Y';        // Формат с полным названием месяца
    public const TEXT_MONTH_FULL_US    = 'F d Y';        // Западный формат с полным названием месяца

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
