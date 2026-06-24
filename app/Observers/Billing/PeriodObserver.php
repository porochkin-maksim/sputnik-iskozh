<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Models\Billing\Period;
use App\Observers\AbstractObserver;
use Core\Domains\HistoryChanges\HistoryType;

class PeriodObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::PERIOD;
    }

    protected function getPropertyTitles(): array
    {
        return Period::PROPERTIES_TO_TITLES;
    }
}
