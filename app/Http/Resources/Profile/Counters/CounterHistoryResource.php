<?php

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Enums\DateTimeFormat;

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
            'id'     => $this->counterHistory->getId(),
            'value'  => $this->counterHistory->getValue(),
            'before' => $this->previousCounterHistory?->getValue(),
            'delta'  => $this?->previousCounterHistory ? ($this->counterHistory->getValue() - $this?->previousCounterHistory->getValue()) : null,
            'date'   => $this->counterHistory->getDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT),
            'days'   => $this?->previousCounterHistory ? $this->counterHistory->getDate()?->diffInDays($this?->previousCounterHistory->getDate()) : null,
            'file'   => $this->counterHistory->getFile(),
        ];
    }
}