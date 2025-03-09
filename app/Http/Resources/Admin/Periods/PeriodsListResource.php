<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Periods;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Period\Collections\PeriodCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

readonly class PeriodsListResource extends AbstractResource
{
    public function __construct(
        private PeriodCollection $periodCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [
            'periods'    => [],
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::PERIOD,
            ),
        ];

        foreach ($this->periodCollection as $period) {
            $result['periods'][] = new PeriodResource($period);
        }

        return $result;
    }
}
