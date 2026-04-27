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
        private CounterHistoryDTO $counterHistory,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $claim = $this->counterHistory->getClaim();

        $date         = clone $this->counterHistory->getDate();
        $previous     = $this->counterHistory->getPrevious();
        $canCreateNew = $date?->endOfDay()?->lte(Carbon::now()->startOfDay()->subDays(7));

        return [
            'id'         => $this->counterHistory->getId(),
            'counterId'  => $this->counterHistory->getCounterId(),
            'value'      => $this->counterHistory->getValue(),
            'isVerified' => $this->counterHistory->isVerified(),
            'before'     => $previous?->getValue(),
            'delta'      => $previous ? ($this->counterHistory->getValue() - $previous->getValue()) : null,
            'date'       => $this->counterHistory->getDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'days'       => $previous ? abs((int) $this->counterHistory->getDate()?->diffInDays($previous->getDate())) : null,
            'file'       => $this->counterHistory->getFile(),
            'actions'    => [
                ResponsesEnum::CREATE => $canCreateNew,
            ],
            'claim'      => $claim ? new ClaimResource($claim) : null,
        ];
    }
}