<?php declare(strict_types=1);

namespace Core\Helpers\DateTime;

use Carbon\Carbon;

abstract class DateTimeHelper
{
    public static function toCarbonOrNull(mixed $date): ?Carbon
    {
        if ($date instanceof Carbon) {
            return $date;
        }

        if ($date instanceof \DateTime) {
            return new Carbon($date);
        }

        if (is_string($date)) {
            try {
                return Carbon::parse($date);
            }
            catch (\Exception) {
                return null;
            }
        }

        return null;
    }
}
