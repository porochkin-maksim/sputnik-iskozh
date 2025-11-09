<?php declare(strict_types=1);

namespace Core\Helpers\DateTime;

use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Exception;

class DateTimeHelper
{
    // Родительный падеж месяцев (для конструкций типа «5 ноября»)
    private const array MONTH_GENITIVE = [
        1  => 'января',
        2  => 'февраля',
        3  => 'марта',
        4  => 'апреля',
        5  => 'мая',
        6  => 'июня',
        7  => 'июля',
        8  => 'августа',
        9  => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря',
    ];

    // Именительный падеж месяцев (для заголовков, списков)
    private const array MONTH_NOMINATIVE = [
        1  => 'Январь',
        2  => 'Февраль',
        3  => 'Март',
        4  => 'Апрель',
        5  => 'Май',
        6  => 'Июнь',
        7  => 'Июль',
        8  => 'Август',
        9  => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];

    // Сокращённые названия месяцев (3 буквы)
    private const array MONTH_ABBREV = [
        1  => 'янв',
        2  => 'фев',
        3  => 'мар',
        4  => 'апр',
        5  => 'май',
        6  => 'июн',
        7  => 'июл',
        8  => 'авг',
        9  => 'сен',
        10 => 'окт',
        11 => 'ноя',
        12 => 'дек',
    ];

    private const array DAY_OF_WEEK_ABBREV = [
        0 => 'вс', // воскресенье
        1 => 'пн', // понедельник
        2 => 'вт', // вторник
        3 => 'ср', // среда
        4 => 'чт', // четверг
        5 => 'пт', // пятница
        6 => 'сб', // суббота
    ];

    /**
     * Возвращает месяц в родительном падеже (например, «ноября»)
     */
    public static function monthGenitive(Carbon $date): string
    {
        return self::MONTH_GENITIVE[$date->month] ?? '?';
    }

    /**
     * Возвращает месяц в именительном падеже (например, «Ноябрь»)
     */
    public static function monthNominative(Carbon $date): string
    {
        return self::MONTH_NOMINATIVE[$date->month] ?? '?';
    }

    /**
     * Возвращает сокращённое название месяца (например, «ноя»)
     */
    public static function monthAbbrev(Carbon $date): string
    {
        return self::MONTH_ABBREV[$date->month] ?? '?';
    }

    /**
     * Форматирует дату в виде «5 ноября 2025»
     */
    public static function formatDayMonthYear(Carbon $date): string
    {
        return sprintf(
            '%d %s %d',
            $date->day,
            self::monthGenitive($date),
            $date->year
        );
    }

    public static function dayOfWeekAbbrev(Carbon $date): string
    {
        $dayOfWeek = (int) $date->format('w'); // 0=воскресенье, 1=понедельник, ..., 6=суббота
        return self::DAY_OF_WEEK_ABBREV[$dayOfWeek] ?? '?';
    }

    public static function toCarbonOrNull(mixed $date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        if ($date instanceof Carbon) {
            return $date;
        }

        if ($date instanceof \DateTime) {
            return new Carbon($date);
        }

        if ( ! is_string($date)) {
            return null;
        }

        try {
            // Удаляем все пробелы и мусор в конце строки
            $date = trim($date, " \t\n\r\0\x0B.,;");

            // Пробуем все форматы из DateTimeFormat
            foreach (DateTimeFormat::constants() as $format) {
                try {
                    $result = Carbon::createFromFormat($format, $date);
                    if ($result !== false) {
                        return $result;
                    }
                }
                catch (Exception) {
                }
            }

            // Если ни один формат не подошел, пробуем стандартный парсинг Carbon
            try {
                return Carbon::parse($date);
            }
            catch (Exception $e) {
                return null;
            }
        }
        catch (Exception $e) {
            return null;
        }
    }
}
