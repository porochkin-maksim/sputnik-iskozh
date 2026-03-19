<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Models\Billing\Period;
use App\Observers\AbstractObserver;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;

class PeriodObserver extends AbstractObserver
{
    protected function getHistoryType(): HistoryType
    {
        return HistoryType::PERIOD;
    }

    protected function getPropertyTitles(): array
    {
        return Period::PROPERTIES_TO_TITLES;
    }
}
