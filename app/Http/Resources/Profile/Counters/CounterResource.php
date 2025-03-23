<?php

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Enums\DateTimeFormat;
use Core\Responses\ResponsesEnum;

readonly class CounterResource extends AbstractResource
{
    public function __construct(
        private CounterDTO $counter,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = \lc::roleDecorator();
        $lastHistory = $this->counter->getHistoryCollection()->last();

        return [
            'id'          => $this->counter->getId(),
            'number'      => $this->counter->getNumber(),
            'isInvoicing' => $this->counter->isInvoicing(),
            'value'       => $lastHistory?->getValue(),
            'date'        => $lastHistory?->getDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT),
            'history'     => new CounterHistoryListResource($this->counter->getHistoryCollection()),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::COUNTERS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::COUNTERS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::COUNTERS_DROP),
            ],
        ];
    }
}