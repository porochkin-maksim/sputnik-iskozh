<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use App\Resources\RouteNames;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Counter\CounterEntity;
use Core\Shared\Helpers\DateTime\DateTimeFormat;

readonly class CounterResource extends AbstractResource
{
    public function __construct(
        private CounterEntity $counter,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $lastHistory = $this->counter->getHistoryCollection()->first();

        return [
            'id'          => $this->counter->getId(),
            'number'      => $this->counter->getNumber(),
            'isInvoicing' => $this->counter->isInvoicing() && $this->counter->getAccountId() !== AccountIdEnum::SNT->value,
            'accountId'   => $this->counter->getAccountId(),
            'increment'   => $this->counter->getIncrement(),
            'value'       => $lastHistory?->getValue(),
            'date'        => $lastHistory?->getDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'history'     => new CounterHistoryListResource($this->counter->getHistoryCollection()),
            'passport'    => $this->counter->getPasportFile(),
            'expireAt'    => $this->counter->getExpireAt()?->format(DateTimeFormat::DATE_DEFAULT),
            'viewUrl'     => route(RouteNames::PROFILE_COUNTER_VIEW, [$this->counter->getUid()]),
        ];
    }
}
