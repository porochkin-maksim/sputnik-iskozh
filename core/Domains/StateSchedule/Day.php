<?php declare(strict_types=1);

namespace Core\Domains\StateSchedule;

use Carbon\Carbon;
use Core\Helpers\DateTime\DateTimeHelper;
use Illuminate\Support\Str;
use JsonSerializable;

readonly class Day implements JsonSerializable
{
    public function __construct(
        private \DatePeriod $period,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'day'           => $this->period->start->format('d'),
            'monthGenitive' => Str::ucfirst(DateTimeHelper::monthGenitive(Carbon::parse($this->period->start))),
            'weekDaylable'  => DateTimeHelper::dayOfWeekAbbrev(Carbon::parse($this->period->start)),
            'startTime'     => $this->period->start->format('H:i'),
            'endTime'       => $this->period->end->format('H:i'),
        ];
    }
}
