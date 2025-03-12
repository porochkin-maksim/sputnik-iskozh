<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Periods;

use app;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Period\Collections\PeriodCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class PeriodsListResource extends AbstractResource
{
    public function __construct(
        private PeriodCollection $periodCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = app::roleDecorator();
        $result = [
            'periods'    => [],
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::PERIOD,
            ),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::PERIODS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::PERIODS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::PERIODS_DROP),
            ],
        ];

        foreach ($this->periodCollection as $period) {
            $result['periods'][] = new PeriodResource($period);
        }

        return $result;
    }
}
