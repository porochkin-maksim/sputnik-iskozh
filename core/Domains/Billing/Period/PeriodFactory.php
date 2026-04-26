<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period;

use Carbon\Carbon;

readonly class PeriodFactory
{
    public function makeDefault(): PeriodEntity
    {
        $year = Carbon::now()->year;

        return $this->makeEmpty()
            ->setName((string) $year)
            ->setStartAt(Carbon::createFromDate($year, 1, 1)->startOfDay())
            ->setEndAt(Carbon::createFromDate($year, 12, 31)->endOfDay());
    }

    public function makeEmpty(): PeriodEntity
    {
        return (new PeriodEntity())->setIsClosed(false);
    }
}
