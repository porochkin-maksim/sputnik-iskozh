<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Periods;

use app;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class PeriodResource extends AbstractResource
{
    public function __construct(
        private PeriodDTO $period,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = app::roleDecorator();
        return [
            'id'         => $this->period->getId(),
            'name'       => $this->period->getName(),
            'startAt'    => $this->formatDateTimeOrNow($this->period->getStartAt()),
            'endAt'      => $this->formatDateTimeOrNow($this->period->getEndAt()),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::PERIODS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::PERIODS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::PERIODS_DROP),
            ],
            'historyUrl' => $this->period->getId()
                ? HistoryChangesLocator::route(
                    type: HistoryType::PERIOD,
                    primaryId: $this->period->getId(),
                ) : null,
        ];
    }
}
