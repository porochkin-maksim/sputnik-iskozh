<?php

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Enums\DateTimeFormat;

readonly class CounterResource extends AbstractResource
{
    public function __construct(
        private CounterDTO $counter,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $lastHistory = $this->counter->getHistoryCollection()->first();

        return [
            'id'          => $this->counter->getId(),
            'number'      => $this->counter->getNumber(),
            'isInvoicing' => $this->counter->isInvoicing(),
            'value'       => $lastHistory?->getValue(),
            'date'        => $lastHistory?->getDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT),
            'history'     => new CounterHistoryListResource($this->counter->getHistoryCollection()),
        ];
    }
}