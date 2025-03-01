<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Periods;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Period\Collections\PeriodCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Resources\RouteNames;

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
            'historyUrl' => route(RouteNames::HISTORY_CHANGES, [
                'type' => HistoryType::PERIOD,
            ]),
        ];

        foreach ($this->periodCollection as $period) {
            $result['periods'][] = new PeriodResource($period);
        }

        return $result;
    }
}
