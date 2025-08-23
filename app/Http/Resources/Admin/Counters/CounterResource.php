<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Accounts\AccountResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\Enums\AccountIdEnum;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Enums\DateTimeFormat;
use Core\Resources\RouteNames;
use Core\Responses\ResponsesEnum;
use lc;

readonly class CounterResource extends AbstractResource
{
    public function __construct(
        private CounterDTO $counter,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access      = lc::roleDecorator();
        $lastHistory = $this->counter->getHistoryCollection()->first();

        return [
            'id'          => $this->counter->getId(),
            'number'      => $this->counter->getNumber(),
            'isInvoicing' => $this->counter->isInvoicing() && $this->counter->getAccountId() !== AccountIdEnum::SNT->value,
            'accountId'   => $this->counter->getAccountId(),
            'increment'   => $this->counter->getIncrement(),
            'value'       => $lastHistory?->getValue(),
            'date'        => $lastHistory?->getDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT),
            'history'     => new CounterHistoryListResource($this->counter->getHistoryCollection()),
            'account'     => $this->counter->getAccount() ? new AccountResource($this->counter->getAccount()) : null,
            'actions'     => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::COUNTERS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::COUNTERS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::COUNTERS_DROP),
            ],
            'viewUrl'     => route(RouteNames::ADMIN_COUNTER_VIEW, [$this->counter->getAccountId(), $this->counter->getId()]),
            'historyUrl'  => $this->counter->getId()
                ? HistoryChangesLocator::route(
                    type     : HistoryType::COUNTER,
                    primaryId: $this->counter->getId(),
                ) : null,
        ];
    }
}