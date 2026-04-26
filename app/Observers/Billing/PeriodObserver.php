<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Models\Billing\Period;
use App\Observers\AbstractObserver;
use Core\Domains\Billing\Jobs\CreateMainServicesJob;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * @var Period $item
     */
    public function created(Model $item): void
    {
        parent::created($item);

        CreateMainServicesJob::dispatch($item->id);
    }
}
