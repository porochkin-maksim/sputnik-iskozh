<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Periods;

use App\Resources\RouteNames;
use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\HistoryChanges\HistoryType;

readonly class PeriodResource extends AbstractResource
{
    public function __construct(
        private PeriodEntity $period,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->period->getId(),
            'name'       => $this->period->getName(),
            'startAt'    => $this->formatDateTimeOrNowForFront($this->period->getStartAt()),
            'endAt'      => $this->formatDateTimeOrNowForFront($this->period->getEndAt()),
            'isClosed'   => $this->period->isClosed(),
            'receiptUrl' => route(RouteNames::DOCUMENT_RECEIPT_BLANK, ['period' => $this->period->getId()]),
            'historyUrl' => $this->period->getId()
                ? HistoryChangesRoute::make(
                    type     : HistoryType::PERIOD,
                    primaryId: $this->period->getId(),
                ) : null,
        ];
    }
}
