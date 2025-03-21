<?php

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use Carbon\Carbon;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Enums\DateTimeFormat;
use Core\Responses\ResponsesEnum;

readonly class CounterHistoryResource extends AbstractResource
{
    public function __construct(
        private CounterHistoryDTO  $counterHistory,
        private ?CounterHistoryDTO $previousCounterHistory = null,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->counterHistory->getId(),
            'value'      => $this->counterHistory->getValue(),
            'isVerified' => $this->counterHistory->isVerified(),
            'before'     => $this->previousCounterHistory?->getValue(),
            'delta'      => $this?->previousCounterHistory ? ($this->counterHistory->getValue() - $this?->previousCounterHistory->getValue()) : null,
            'date'       => $this->counterHistory->getDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT),
            'days'       => $this?->previousCounterHistory ? $this->counterHistory->getDate()?->diffInDays($this?->previousCounterHistory->getDate()) : null,
            'file'       => $this->counterHistory->getFile(),
            'actions'    => [
                ResponsesEnum::CREATE => (bool) $this?->previousCounterHistory?->getDate()?->endOfDay()?->lte(Carbon::now()->endOfDay()),
            ],
        ];
    }
}