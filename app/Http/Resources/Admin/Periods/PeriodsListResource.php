<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Periods;

use lc;
use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\HistoryChanges\HistoryType;

readonly class PeriodsListResource extends AbstractResource
{
    public function __construct(
        private PeriodCollection $periodCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $result = [
            'periods'    => [],
            'historyUrl' => HistoryChangesRoute::make(
                type: HistoryType::PERIOD,
            ),
            'actions'    => [
                'view' => $access->can(PermissionEnum::PERIODS_VIEW),
                'edit' => $access->can(PermissionEnum::PERIODS_EDIT),
                'drop' => $access->can(PermissionEnum::PERIODS_DROP),
            ],
        ];

        $hasUnclosed = false;
        foreach ($this->periodCollection as $period) {
            $result['periods'][] = new PeriodResource($period);
            $hasUnclosed         = $hasUnclosed || ! $period->isClosed();
        }

        $result['actions']['create'] = ! $hasUnclosed;

        return $result;
    }
}
