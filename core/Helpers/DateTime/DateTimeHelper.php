<?php declare(strict_types=1);

namespace Core\Helpers\DateTime;

use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Exception;

class DateTimeHelper
{
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
