<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Account\Enums\AccountIdEnum;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Core\Enums\DateTimeFormat;
use Core\Resources\RouteNames;

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
            'isInvoicing' => $this->counter->isInvoicing() && $this->counter->getAccountId() !== AccountIdEnum::SNT->value,
            'accountId'   => $this->counter->getAccountId(),
            'value'       => $lastHistory?->getValue(),
            'date'        => $lastHistory?->getDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'history'     => new CounterHistoryListResource($this->counter->getHistoryCollection()),
            'viewUrl'     => route(RouteNames::PROFILE_COUNTER_VIEW, ['counter' => UidFacade::getUid(UidTypeEnum::COUNTER, $this->counter->getId())]),
        ];
    }
}