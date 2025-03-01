<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Periods;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Resources\RouteNames;

readonly class PeriodResource extends AbstractResource
{
    public function __construct(
        private PeriodDTO $period,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->period->getId(),
            'name'       => $this->period->getName(),
            'startAt'    => $this->formatDateTimeOrNow($this->period->getStartAt()),
            'endAt'      => $this->formatDateTimeOrNow($this->period->getEndAt()),
            'actions'    => [
                'drop' => true,
            ],
            'historyUrl' => $this->period->getId() ? route(RouteNames::HISTORY_CHANGES, [
                'type'       => HistoryType::PERIOD,
                'primaryId' => $this->period->getId(),
            ]) : null,
        ];
    }
}
