<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Profile\Claims\ClaimResource;
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
        $claim = $this->counterHistory->getClaim();

        return [
            'id'         => $this->counterHistory->getId(),
            'counterId'  => $this->counterHistory->getCounterId(),
            'value'      => $this->counterHistory->getValue(),
            'isVerified' => $this->counterHistory->isVerified(),
            'before'     => $this->previousCounterHistory?->getValue(),
            'delta'      => $this?->previousCounterHistory ? ($this->counterHistory->getValue() - $this?->previousCounterHistory->getValue()) : null,
            'date'       => $this->counterHistory->getDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'days'       => $this?->previousCounterHistory ? $this->counterHistory->getDate()?->diffInDays($this?->previousCounterHistory->getDate()) : null,
            'file'       => $this->counterHistory->getFile(),
            'actions'    => [
                ResponsesEnum::CREATE => ! $this?->previousCounterHistory || (bool) $this?->previousCounterHistory?->getDate()?->endOfDay()?->lte(Carbon::now()->startOfDay()),
            ],
            'claim'      => $claim ? new ClaimResource($claim) : null,
        ];
    }
}