<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Accounts\AccountResource;
use App\Resources\RouteNames;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use lc;

readonly class CounterResource extends AbstractResource
{
    public function __construct(
        private CounterEntity $counter,
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
            'expireAt'    => $this->counter->getExpireAt()?->format(DateTimeFormat::DATE_DEFAULT),
            'passport'    => $this->counter->getPasportFile(),
            'history'     => new CounterHistoryListResource($this->counter->getHistoryCollection()),
            'account'     => $this->counter->getAccount() ? new AccountResource($this->counter->getAccount()) : null,
            'actions'     => [
                'view' => $access->can(PermissionEnum::COUNTERS_VIEW),
                'edit' => $access->can(PermissionEnum::COUNTERS_EDIT),
                'drop' => $access->can(PermissionEnum::COUNTERS_DROP),
            ],
            'viewUrl'     => route(RouteNames::ADMIN_COUNTER_VIEW, [$this->counter->getAccountId(), $this->counter->getId()]),
            'historyUrl'  => $this->counter->getId()
                ? HistoryChangesRoute::make(
                    type     : HistoryType::COUNTER,
                    primaryId: $this->counter->getId(),
                ) : null,
        ];
    }
}
