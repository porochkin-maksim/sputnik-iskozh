<?php declare(strict_types=1);

namespace Core\Domains\StateSchedule;

use Carbon\Carbon;
use DatePeriod;
use DateInterval;

abstract class StateSchedule
{
    private const int HOUR_START = 12;
    private const int HOUR_END   = 14;

    /**
     * @return DatePeriod[]
     */
    public static function getScheduledDates(Carbon $startDate, int $numDates = 10): array
    {
        $dates       = [];
        $currentDate = clone $startDate;
        $currentDate->startOfDay();

        $countDates = 0;
        while ($countDates < $numDates) {
            $month      = (int) $currentDate->format('n');
            $dayOfWeek  = (int) $currentDate->format('w'); // 0=воскресенье

            $isWarmPeriod = ($month >= 4 && $month <= 10);

            if ($isWarmPeriod) {
                // Апрель–октябрь: четверг (4) и воскресенье (0)
                if ($dayOfWeek === 4 || $dayOfWeek === 0) {
                    $start = clone $currentDate;
                    $start->hour(self::HOUR_START)->minute(0)->second(0);

                    $end = clone $currentDate;
                    $end->hour(self::HOUR_END)->minute(0)->second(0);

                    $dates[]  = new Day(new DatePeriod($start, new DateInterval('PT1H'), $end));
                }
            }
            // Ноябрь–март: 1‑е и 3‑е воскресенья
            elseif ($dayOfWeek === 0) {
                $firstDayOfMonth   = Carbon::create($currentDate->year, $currentDate->month, 1);
                $firstSunday       = clone $firstDayOfMonth;
                $daysToFirstSunday = (7 - $firstDayOfMonth->dayOfWeek) % 7;
                $firstSunday->addDays($daysToFirstSunday);

                $sundayCount = 1;
                $temp        = clone $firstSunday;
                while ($temp->lt($currentDate)) {
                    $temp->addWeek();
                    $sundayCount++;
                }

                if ($sundayCount === 1 || $sundayCount === 3) {
                    $start = clone $currentDate;
                    $start->hour(self::HOUR_START)->minute(0)->second(0);

                    $end = clone $currentDate;
                    $end->hour(self::HOUR_END)->minute(0)->second(0);

                    $dates[]  = new Day(new DatePeriod($start, new DateInterval('PT1H'), $end));
                }
            }

            // Переходим к следующему дню
            $countDates = count($dates);
            $currentDate->addDay();
        }

        return $dates;
    }
}
